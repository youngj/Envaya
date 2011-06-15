<?php

/*
 * A simple standalone HTTP server for development that serves PHP scripts and static files.
 *
 * Each PHP request will be run in an isolated environment using PHP-CGI.
 * (The 'php-cgi' binary must be installed on the local machine, and in the PATH.)
 *
 * It is not very fast or robust, and may have security flaws, and should never be 
 * used in production.
 *
 * This allows running PHP scripts without needing to install a web server like Apache or Nginx.
 * It also allows selenium tests to spawn a HTTP server with custom configuration settings.
 *
 * WebServer only depends on code in this directory, and no other Envaya code,
 * so it could easily be extracted and used in other PHP projects that need a standalone HTTP server.
 */
 
require_once __DIR__."/httprequest.php";
require_once __DIR__."/httpresponse.php";

class WebServer
{
    /* 
     * The following properties can be passed as options to the constructor: 
     */
    
    public $port = 80;                  // TCP port number to listen on
    
    public $document_root = '/var/www'; // the root directory out of which requests will be served
    
    public $static_regexes = array();   // a list of string regexes; if the URI matches, 
                                        // it will be served from $document_root as a static file
    
    public $php_regexes = array();      // a list of string regexes; if the URI matches, 
                                        // it will be served from $document_root as a PHP script
    
    public $php_index = null;           // a URI to a PHP file; if set, any URIs not already matched 
                                        // will be served  from this script with PATH_INFO as the original $uri                                

    private $requests = array(/* socket_id => HTTPRequest */);    

    function __construct($options)
    {
        foreach ($options as $k => $v)
        {
            $this->$k = $v;
        }
    }

    function run_forever()
    {
        if (!sizeof($_ENV))
        {
            error_log("error: \$_ENV is empty. add variables_order=\"GPCSE\" to your php.ini file and try again.\n");
            return;
        }          
    
        set_time_limit(0);

        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
            
        if (@socket_bind($sock, 0, $this->port) == false)
        {
            error_log("Could not start a web server on port {$this->port}.\n"
                ."If you're running Envaya on Apache or Nginx, just ignore this message.\n"
                ."Otherwise, stop the existing server or add a port to your 'domain'.\n"
                ." e.g. add  'domain' => 'localhost:####',  in config/local.php");
            return;
        }

        socket_listen($sock);

        echo "Web server listening on 0.0.0.0:{$this->port} (see http://localhost:{$this->port}/)...\n";    

        socket_set_nonblock($sock);        

        $requests =& $this->requests;
    
        while (true)
        {        
            $read = array();
            $write = array();
            foreach ($requests as $id => $request)
            {
                if (!$request->is_read_complete())
                {
                    $read[] = $request->socket;
                }
                else
                {
                    $write[] = $request->socket;
                }
            }            
            $read[] = $sock;            
            
            if (socket_select($read, $write, $except = null, null) < 1)
                continue;
                
            //echo sizeof($read)." ".sizeof($write)."\n";
                        
            if (in_array($sock, $read))
            {
                $client = socket_accept($sock);
                //echo "accepted $client\n";
                $requests[(int)$client] = new HTTPRequest($client);
                
                $key = array_search($sock, $read);
                unset($read[$key]);
            }
            
            foreach ($read as $client)
            {
                $this->read_socket($client);
            }
            
            foreach ($write as $client)
            {
                $this->write_socket($client);
            }
        }        
    }
    
    function write_socket($client)
    {
        $request = $this->requests[(int)$client];
        $response_buf =& $request->response_buf;     
        $len = @socket_write($client, $response_buf);
        if ($len === null)
        {
            echo "socket_write returned null\n";
            $this->end_request($request);
        }
        else if ($len < strlen($response_buf))
        {
            $response_buf = substr($response_buf, $len);
        }
        else
        {
            $response = $request->response;
            $len = strlen($response->content);
            $client_num = (int)$client;
            echo "($client_num) {$request->method} {$request->request_uri} => {$response->status} {$len}\n";
                         
            if (@$request->headers['Connection'] == 'close')
            {
                $this->end_request($request);
            }
            else
            {
                $this->requests[(int)$client] = $next_request = new HTTPRequest($client);
                //$next_request->add_data($request->leftover_data);
            }
        }                
    }
    
    
    function read_socket($client)
    {
        $request = $this->requests[(int)$client];
        $data = @socket_read($client, 8092, PHP_BINARY_READ);                                
        if ($data === null || $data == '')
        {
            //echo "socket_read did not have data\n";
            $this->end_request($request);
        }
        else
        {
            $request->add_data($data);
            
            if ($request->is_read_complete())
            {
                $response = $this->get_response($request);
                $request->set_response($response);
            }    
        }
    }
    
    function end_request($request)
    {
        @socket_close($request->socket);
        unset($this->requests[(int)$request->socket]);    
    }    
    
    function get_response($request)
    {
        $uri = $request->uri;
        
        // disallow suspicious paths
        if (strpos($uri, '..') !== false || preg_match('#[^\w\.\-/]#', $uri) || $uri[0] != '/')
        {
            return new HTTPResponse(403, "Invalid URI $uri"); 
        }
        
        foreach ($this->static_regexes as $static_regex)
        {
            if (preg_match($static_regex, $uri))
            {
                return $this->get_static_response($request, $uri);
            }
        }
        
        foreach ($this->php_regexes as $php_regex)
        {
            if (preg_match($php_regex, $uri))
            {
                return $this->get_php_response($request, $uri);
            }
        }
        
        if ($this->php_index)
        {
            return $this->get_php_response($request, $this->php_index, $uri);
        }
        else
        {   
            return new HTTPResponse(404, "File not found");
        }
    }        
    	
    private function get_static_response($request, $uri)
    {   
        $local_path = "{$this->document_root}$uri";
        
        if (is_file($local_path))
        {
            return new HTTPResponse(200, 
                file_get_contents($local_path),
                array(
                    'Content-Type' => static::get_mime_type($local_path),
                    'Cache-Control' => "max-age=8640000"
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
        
    static function parse_headers($headers_str)
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
        
    private function get_php_response($request, $uri, $path_info = null)
    {
        $script_filename = "{$this->document_root}$uri";        
        if (!is_file($script_filename))
        {
            return new HTTPResponse(404, "File not found");
        }    
        
        $headers = $request->headers;
        $content_length = @$headers['Content-Length'];        
        
        // see http://www.faqs.org/rfcs/rfc3875.html
        $cgi_env = array(
            'QUERY_STRING' => $request->query_string,
            'REQUEST_METHOD' => $request->method,
            'REQUEST_URI' => $request->request_uri,
            'PATH_INFO' => $path_info,
            'REDIRECT_STATUS' => 200,
            'SCRIPT_NAME' => $uri,
            'SERVER_NAME' => @$headers['Host'],
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'SERVER_SOFTWARE' => 'Envaya/0.1',
            'SCRIPT_FILENAME' => $script_filename,
            'DOCUMENT_ROOT' => $this->document_root,
            'CONTENT_TYPE' => @$headers['Content-Type'],
            'CONTENT_LENGTH' => $content_length,            
        );
        
        foreach ($headers as $name => $value)
        {        
            $name = str_replace('-','_', $name);
            $name = strtoupper($name);
            $cgi_env["HTTP_$name"] = $value;
        }

        if ($content_length)
        {
            $content_stream = tmpfile();
            fwrite($content_stream, $request->content);
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
            array('binary_pipes' => true)
        );                        
                
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
        
    static function get_mime_type($filename)
    {
        $pathinfo = pathinfo($filename);
        $extension = strtolower($pathinfo['extension']);
    
        return @static::$mime_types[$extension];
    }    
    
	static $mime_types = array("323" => "text/h323", "acx" => "application/internet-property-stream", "ai" => "application/postscript", "aif" => "audio/x-aiff", "aifc" => "audio/x-aiff", "aiff" => "audio/x-aiff",
        "asf" => "video/x-ms-asf", "asr" => "video/x-ms-asf", "asx" => "video/x-ms-asf", "au" => "audio/basic", "avi" => "video/quicktime", "axs" => "application/olescript", "bas" => "text/plain", "bcpio" => "application/x-bcpio", "bin" => "application/octet-stream", "bmp" => "image/bmp",
        "c" => "text/plain", "cat" => "application/vnd.ms-pkiseccat", "cdf" => "application/x-cdf", "cer" => "application/x-x509-ca-cert", "class" => "application/octet-stream", "clp" => "application/x-msclip", "cmx" => "image/x-cmx", "cod" => "image/cis-cod", "cpio" => "application/x-cpio", "crd" => "application/x-mscardfile",
        "crl" => "application/pkix-crl", "crt" => "application/x-x509-ca-cert", "csh" => "application/x-csh", "css" => "text/css", "dcr" => "application/x-director", "der" => "application/x-x509-ca-cert", "dir" => "application/x-director", "dll" => "application/x-msdownload", "dms" => "application/octet-stream", "doc" => "application/msword",
        "dot" => "application/msword", "dvi" => "application/x-dvi", "dxr" => "application/x-director", "eps" => "application/postscript", "etx" => "text/x-setext", "evy" => "application/envoy", "exe" => "application/octet-stream", "fif" => "application/fractals", "flr" => "x-world/x-vrml", "gif" => "image/gif",
        "gtar" => "application/x-gtar", "gz" => "application/x-gzip", "h" => "text/plain", "hdf" => "application/x-hdf", "hlp" => "application/winhlp", "hqx" => "application/mac-binhex40", "hta" => "application/hta", "htc" => "text/x-component", "htm" => "text/html", "html" => "text/html",
        "htt" => "text/webviewhtml", "ico" => "image/x-icon", "ief" => "image/ief", "iii" => "application/x-iphone", "ins" => "application/x-internet-signup", "isp" => "application/x-internet-signup", "jfif" => "image/pipeg", "jpe" => "image/jpeg", "jpeg" => "image/jpeg", "jpg" => "image/jpeg",
        "js" => "application/x-javascript", "latex" => "application/x-latex", "lha" => "application/octet-stream", "lsf" => "video/x-la-asf", "lsx" => "video/x-la-asf", "lzh" => "application/octet-stream", "m13" => "application/x-msmediaview", "m14" => "application/x-msmediaview", "m3u" => "audio/x-mpegurl", "man" => "application/x-troff-man",
        "mdb" => "application/x-msaccess", "me" => "application/x-troff-me", "mht" => "message/rfc822", "mhtml" => "message/rfc822", "mid" => "audio/mid", "mny" => "application/x-msmoney", "mov" => "video/quicktime", "movie" => "video/x-sgi-movie", "mp2" => "video/mpeg", "mp3" => "audio/mpeg",
        "mpa" => "video/mpeg", "mpe" => "video/mpeg", "mpeg" => "video/mpeg", "mpg" => "video/mpeg", "mpp" => "application/vnd.ms-project", "mpv2" => "video/mpeg", "ms" => "application/x-troff-ms", "mvb" => "application/x-msmediaview", "nws" => "message/rfc822", "oda" => "application/oda",
        "p10" => "application/pkcs10", "p12" => "application/x-pkcs12", "p7b" => "application/x-pkcs7-certificates", "p7c" => "application/x-pkcs7-mime", "p7m" => "application/x-pkcs7-mime", "p7r" => "application/x-pkcs7-certreqresp", "p7s" => "application/x-pkcs7-signature", "pbm" => "image/x-portable-bitmap", "pdf" => "application/pdf", "pfx" => "application/x-pkcs12",
        "pgm" => "image/x-portable-graymap", "pko" => "application/ynd.ms-pkipko", "pma" => "application/x-perfmon", "pmc" => "application/x-perfmon", "pml" => "application/x-perfmon", "pmr" => "application/x-perfmon", "pmw" => "application/x-perfmon", "png" => "image/png", "pnm" => "image/x-portable-anymap", "pot" => "application/vnd.ms-powerpoint", "ppm" => "image/x-portable-pixmap",
        "pps" => "application/vnd.ms-powerpoint", "ppt" => "application/vnd.ms-powerpoint", "prf" => "application/pics-rules", "ps" => "application/postscript", "pub" => "application/x-mspublisher", "qt" => "video/quicktime", "ra" => "audio/x-pn-realaudio", "ram" => "audio/x-pn-realaudio", "ras" => "image/x-cmu-raster", "rgb" => "image/x-rgb",
        "rmi" => "audio/mid", "roff" => "application/x-troff", "rtf" => "application/rtf", "rtx" => "text/richtext", "scd" => "application/x-msschedule", "sct" => "text/scriptlet", "setpay" => "application/set-payment-initiation", "setreg" => "application/set-registration-initiation", "sh" => "application/x-sh", "shar" => "application/x-shar",
        "sit" => "application/x-stuffit", "snd" => "audio/basic", "spc" => "application/x-pkcs7-certificates", "spl" => "application/futuresplash", "src" => "application/x-wais-source", "sst" => "application/vnd.ms-pkicertstore", "stl" => "application/vnd.ms-pkistl", "stm" => "text/html", "svg" => "image/svg+xml", "sv4cpio" => "application/x-sv4cpio",
        "sv4crc" => "application/x-sv4crc", "t" => "application/x-troff", "tar" => "application/x-tar", "tcl" => "application/x-tcl", "tex" => "application/x-tex", "texi" => "application/x-texinfo", "texinfo" => "application/x-texinfo", "tgz" => "application/x-compressed", "tif" => "image/tiff", "tiff" => "image/tiff",
        "tr" => "application/x-troff", "trm" => "application/x-msterminal", "tsv" => "text/tab-separated-values", "txt" => "text/plain", "uls" => "text/iuls", "ustar" => "application/x-ustar", "vcf" => "text/x-vcard", "vrml" => "x-world/x-vrml", "wav" => "audio/x-wav", "wcm" => "application/vnd.ms-works",
        "wdb" => "application/vnd.ms-works", "wks" => "application/vnd.ms-works", "wmf" => "application/x-msmetafile", "wps" => "application/vnd.ms-works", "wri" => "application/x-mswrite", "wrl" => "x-world/x-vrml", "wrz" => "x-world/x-vrml", "xaf" => "x-world/x-vrml", "xbm" => "image/x-xbitmap", "xla" => "application/vnd.ms-excel",
        "xlc" => "application/vnd.ms-excel", "xlm" => "application/vnd.ms-excel", "xls" => "application/vnd.ms-excel", "xlt" => "application/vnd.ms-excel", "xlw" => "application/vnd.ms-excel", "xof" => "x-world/x-vrml", "xpm" => "image/x-xpixmap", "xwd" => "image/x-xwindowdump", "z" => "application/x-compress", "zip" => "application/zip");    
}
