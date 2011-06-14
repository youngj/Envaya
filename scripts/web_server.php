<?php

/*
 * A simple standalone HTTP server for development.
 * Each PHP request will be run in an isolated environment using PHP-CGI.
 * (The 'php-cgi' binary must be installed on the local machine.)
 *
 * It is not very fast, robust, and may have security flaws, and should never be 
 * used in production.
 *
 * Just run it on the command line like "php web_server.php".
 *
 * This allows running Envaya without needing a web server like Apache or Nginx.
 * It also allows selenium tests to spawn a HTTP server with custom configuration settings.
 */

require __DIR__."/../start.php";

function run_server()
{
    if (!sizeof($_ENV))
    {
        echo "error: \$_ENV is empty. add variables_order=\"GPCSE\" to your php.ini file and try again.\n";
        die;
    }

    $domain = Config::get('domain');
    $domain_parts = explode(':', $domain, 2);
    $port = isset($domain_parts[1]) ? ((int)$domain_parts[1]) : 80;
    
    set_time_limit(0);

    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
        
    if (@socket_bind($sock, 0, $port) == false)
    {
        echo "Could not start a web server on port $port.\n";
        echo "If you're running Envaya on Apache or Nginx, just ignore this message.\n";
        echo "Otherwise, stop the existing server process or change 'domain' in config/local.php\n";
        exit;
    }

    socket_listen($sock);

    echo "Web server listening on 0.0.0.0:$port (see http://localhost:$port/)...\n";    
    
    while (true)
    {
        $client = socket_accept($sock);
        print "accept $client\n";
                
        if (!$client)
        {
            continue;
        }
        
        try
        {
            $request = new HTTPRequest($client);
            $response = $request->get_response();
        }
        catch (BadRequestException $ex)
        {
            $response = new HTTPResponse(400, "Bad Request: {$ex->getMessage()}");
        }        
        
        $len = strlen($response->content);
        
        echo "{$request->method} {$request->request_uri} => {$response->status} {$len}\n";
        
        $response_str = $response->render();
               
        $len = @socket_write($client, $response_str);

        @socket_close($client);        
    }
}

class BadRequestException extends Exception {}

class HTTPRequest
{
    const BUFFER_SIZE = 8092;

    public $method;             // HTTP method, e.g. "GET" or "POST"
    public $request_uri;        // original requested URI, with query string
    public $uri;                // path component of URI, without query string
    public $http_version;       // version from the request line, e.g. "HTTP/1.1"
    public $query_string;       // query string, like "a=b&c=d"
    public $headers;            // associative array of HTTP headers    
    public $content;            // content of POST request, if applicable
    
    function __construct($client)
    {                      
        $this->read_request($client);
    
        $parsed_uri = parse_url($this->request_uri);        
        $this->uri = $parsed_uri['path'];
        $this->query_string = @$parsed_uri['query'];  
    }   
            
    /* 
     * Reads an entire HTTP request from a client socket, setting 
     * headers, content, request_uri, method, and http_version properties
     */
    function read_request($client)
    {
        $request = '';                

        // read HTTP headers from client socket
        while (true)
        {
            $request_chunk = @socket_read($client, static::BUFFER_SIZE, PHP_BINARY_READ);
            if (!$request_chunk)
            {
                throw new BadRequestException("Did not receive complete HTTP headers");
            }
            
            $request .= $request_chunk;
                   
            $end_headers = strpos($request, "\r\n\r\n");
            if ($end_headers !== false)
            {
                break;
            }        
        }
        
        // parse HTTP request line    
        $end_req = strpos($request, "\r\n"); 
        $req_line = substr($request, 0, $end_req);
        $req_arr = explode(' ', $req_line, 3);

        $this->method = $req_arr[0];
        $this->request_uri = $req_arr[1];
        $this->http_version = $req_arr[2];    
                
        // parse HTTP headers
        $start_headers = $end_req + 2;
                
        $headers_str = substr($request, $start_headers, $end_headers - $start_headers);
        $this->headers = $headers = static::parse_headers($headers_str);
            
        // get rest of HTTP content from client socket
        $start_content = $end_headers + 4; // $end_headers is before last \r\n\r\n
        
        $content_len = (int)@$headers['Content-Length'];
        
        $total_request_len = $start_content + $content_len;
        
        if ($content_len)
        {
            while (true)
            {
                $rem_bytes = $total_request_len - strlen($request);
                if ($rem_bytes <= 0)
                {
                    break;
                }
                
                $request_chunk = @socket_read($client, static::BUFFER_SIZE, PHP_BINARY_READ);
                if (!$request_chunk)
                {
                    throw new BadRequestException("Did not receive complete HTTP content");
                }
                
                $request .= $request_chunk;
            }
        }
        
        $this->content = substr($request, $start_content);
    }
        
    private static function parse_headers($headers_str)
    {
        $headers_arr = explode("\r\n", $headers_str);
                
        $headers = array();
        foreach ($headers_arr as $header_str)
        {
            $header_arr = explode(": ", $header_str, 2);
            $header_name = $header_arr[0];            
            $headers[$header_name] = $header_arr[1];
        }                
        return $headers;
    }
        
    function get_static_response()
    {
        $uri = $this->uri;
        
        // disallow suspicious paths
        if (strpos($uri, '..') !== false || preg_match('#[^\w\.\-/]#', $uri) || $uri[0] != '/')
        {
            return new HTTPResponse(403, "Invalid URI $uri"); 
        }
        
        $local_path = Config::get('root')."/www$uri";
        
        if (is_file($local_path))
        {
            return new HTTPResponse(200, 
                file_get_contents($local_path),
                array(
                    'Content-Type' => UploadedFile::get_mime_type($local_path),
                    'Cache-Control' => "max-age=86400"
                )
            );
        }
        else if (is_dir($local_path))
        {
            return new HTTPResponse(403, "Directory listing not allowed");
        }
        else
        {
            return new HTTPResponse(404, "File not found");
        }
    
    }
    
    function get_php_response()
    {
        if (preg_match('#^/(\w+)\.php$#', $this->uri))
        {
            $script_name = $this->uri;
        }
        else
        {
            $script_name = '/index.php';
        }
        $script_filename = Config::get('root') . "/www$script_name";

        if (!is_file($script_filename))
        {
            return new HTTPResponse(404, "File not found");
        }    
        
        $headers = $this->headers;
        $content_length = @$headers['Content-Length'];        
        $cgi_env = array(
            'QUERY_STRING' => $this->query_string,
            'REQUEST_METHOD' => $this->method,
            'REQUEST_URI' => $this->request_uri,
            'PATH_INFO' => $this->uri,
            'REDIRECT_STATUS' => 200,
            'SCRIPT_NAME' => $script_name,
            'SERVER_NAME' => @$headers['Host'],
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'SERVER_SOFTWARE' => 'Envaya/0.1',
            'SCRIPT_FILENAME' => $script_filename,
            'DOCUMENT_ROOT' => Config::get('root'),
            'CONTENT_TYPE' => @$headers['Content-Type'],
            'CONTENT_LENGTH' => $content_length,            
        );
        
        foreach ($this->headers as $name => $value)
        {        
            $name = str_replace('-','_', $name);
            $name = strtoupper($name);
            $cgi_env["HTTP_$name"] = $value;
        }

        if ($content_length)
        {
            $content_stream = tmpfile();
            fwrite($content_stream, $this->content);
            fseek($content_stream, 0);
        }
        else
        {        
            $content_stream = fopen("data://text/plain,", 'rb');
        }
        
        $descriptorspec = array(
           0 => $content_stream,
           1 => array('pipe', 'w'),
           2 => STDOUT, 
        );

        $proc = proc_open("php-cgi", $descriptorspec, $pipes, 
            __DIR__, 
            array_merge($_ENV, $cgi_env),
            array('binary_pipes' => true));                        
                
        ob_start();
        fpassthru($pipes[1]);
        $response_str = ob_get_clean();

        $end_response_headers = strpos($response_str, "\r\n\r\n");
        
        $headers_str = substr($response_str, 0, $end_response_headers);

        $headers = static::parse_headers($headers_str);        
        
        $response = new HTTPResponse();                        
        
        // php-cgi sends HTTP status as regular header
        if (isset($headers['Status']))
        {
            $response->status = (int) $headers['Status'];
            unset($headers['Status']);
        }
        $response->headers = $headers;                        
        $response->content = substr($response_str, $end_response_headers + 4);
        
        proc_close($proc);
        
        fclose($content_stream);
                
        return $response;
    }
        
    function get_response()
    {
        $uri = $this->uri;
        
        if (strpos($uri, '/_media/') === 0)
        {
            return $this->get_static_response();
        }
        else
        {
            return $this->get_php_response();
        }
    }    
}

class HTTPResponse extends Response 
{
    function render()
    {
        $headers = $this->headers;
        $status = $this->status;
        $content = $this->content;

        if (!isset($headers['Content-Length']))
        {
            $headers['Content-Length'] = strlen($content);
        }
        
        $headers['Connection'] = 'close';
        $headers['Server'] = 'Envaya/0.1';        
            
        $status_msg = Response::$messages[$status];

        ob_start();
        
        echo "HTTP/1.1 $status $status_msg\r\n";
        foreach ($headers as $name => $value)
        {
            echo "$name: $value\r\n";
        }
        echo "\r\n";
        echo $content;
        
        return ob_get_clean();
    }
}

run_server();