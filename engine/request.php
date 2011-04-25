<?php
/**
 * Request and response wrapper. 
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Request {

    // HTTP status codes and messages
    public static $messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * @var  object  main request instance
     */
    public static $instance;
    
    /**
     * @var  string  method: GET, POST, PUT, DELETE, etc
     */
    public $method = 'GET';

    /**
     * @var  string  protocol: http, https, ftp, cli, etc
     */
    public $protocol = 'http';

    /**
     * @var  string  referring URL
     */
    public $referrer;

    /**
     * @var  string  client user agent
     */
    public $user_agent = '';

    /**
     * @var  string  client IP address
     */
    public $client_ip = '0.0.0.0';    

    public $custom_domain_username;
   
    /**
     * @var  integer  HTTP response code: 200, 404, 500, etc
     */
    public $status = 200;

    /**
     * @var  string  response body
     */
    public $response = '';

    /**
     * @var  array  headers to send with the response body
     */
    public $headers = array();
    
    /**
     * @var  string the URI of the request, possibly after rewriting
     */
    public $uri;    
    
    public $original_uri;
    
    public $host;
    
    public $query_string;
    
    /**
     * Main request singleton instance, with parameters determined from the
     * http request
     *
     *     $request = Request::instance();
     *
     * @return  Request
     */
    public static function instance()
    {
        if (!Request::$instance)
        {
            $options = array();
                    
            if ( ! empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN))
            {
                // This request is secure
                $options['protocol'] = 'https';
            }

            $options['method'] = $method = @$_SERVER['REQUEST_METHOD'];            
            $options['referrer'] = @$_SERVER['HTTP_REFERER'];
            $options['user_agent'] = @$_SERVER['HTTP_USER_AGENT'];
            $options['client_ip'] = @$_SERVER['HTTP_X_FORWARDED_FOR'] ?: @$_SERVER['HTTP_CLIENT_IP'] ?: @$_SERVER['REMOTE_ADDR'];
            $options['query_string'] = @$_SERVER['QUERY_STRING'] ? "?{$_SERVER['QUERY_STRING']}" : '';            
            $options['host'] = @$_SERVER['HTTP_HOST'];            

            if ($method !== 'GET' AND $method !== 'POST')
            {
                // Methods besides GET and POST do not properly parse the form-encoded
                // query string into the $_POST array, so we overload it manually.
                parse_str(file_get_contents('php://input'), $_POST);
            }           
            
            $uri = $_SERVER['PATH_INFO'];

            // Reduce multiple slashes to a single slash
            $uri = preg_replace('#//+#', '/', $uri);

            // Remove all dot-paths from the URI, they are not valid
            $uri = preg_replace('#\.[\s./]*/#', '', $uri);
            
            Request::$instance = new Request($uri, $options);
        }

        return Request::$instance;
    }

    public function __construct($uri, $options)
    {
        $this->original_uri = $uri;
    
        $host = @$options['host'];    
        $username = OrgDomainName::get_username_for_host($host);
        if ($username)
        {
            $this->custom_domain_username = $username;
            $this->uri = "/{$username}{$uri}";
        }
        else
        {
            $this->uri = $uri;
        }
    
        if ($options)
        {
            foreach ($options as $name => $value)
            {
                $this->$name = $value;
            }
        }
    }

    public function __toString()
    {
        return (string) $this->response;
    }
    
    /**
     * Sends the response status and all set headers. The current server
     * protocol (HTTP/1.0 or HTTP/1.1) will be used when available. If not
     * available, HTTP/1.1 will be used.
     *
     *     $request->send_headers();
     *
     * @return  $this
     * @uses    Request::$messages
     */
    public function send_headers()
    {
        if ( ! headers_sent())
        {
            if (isset($_SERVER['SERVER_PROTOCOL']))
            {
                // Use the default server protocol
                $protocol = $_SERVER['SERVER_PROTOCOL'];
            }
            else
            {
                // Default to using newer protocol
                $protocol = 'HTTP/1.1';
            }

            // HTTP status line
            header($protocol.' '.$this->status.' '.Request::$messages[$this->status]);

            foreach ($this->headers as $name => $value)
            {
                if (is_string($name))
                {
                    // Combine the name and value to make a raw header
                    $value = "{$name}: {$value}";
                }

                // Send the raw header
                header($value, TRUE);
            }
        }

        return $this;
    }

    public function full_original_url()
    {
        return "{$this->protocol}://{$this->host}{$this->original_uri}{$this->query_string}";
    }
    
    public function full_rewritten_url()
    {
        $domain = Config::get('domain');        
        return "{$this->protocol}://$domain/{$this->uri}{$this->query_string}";
    }
    
    public function is_post()
    {
        return $this->method == "POST";
    }
    
    public function is_secure()
    {
        return $this->protocol == 'https';
    }   
}
