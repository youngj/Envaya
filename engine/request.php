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
     * @var  string  method: GET, POST, PUT, DELETE, etc
     */
    public static $method = 'GET';

    /**
     * @var  string  protocol: http, https, ftp, cli, etc
     */
    public static $protocol = 'http';

    /**
     * @var  string  referring URL
     */
    public static $referrer;

    /**
     * @var  string  client user agent
     */
    public static $user_agent = '';

    /**
     * @var  string  client IP address
     */
    public static $client_ip = '0.0.0.0';

    /**
     * @var  boolean  AJAX-generated request
     */
    public static $is_ajax = FALSE;

    /**
     * @var  object  main request instance
     */
    public static $instance;

    private static $custom_domain_username;

    /**
     * Main request singleton instance. If no URI is provided, the URI will
     * be automatically detected using PATH_INFO, REQUEST_URI, or PHP_SELF.
     *
     *     $request = Request::instance();
     *
     * @param   string   URI of the request
     * @return  Request
     */
    public static function instance( & $uri = TRUE)
    {
        if ( ! Request::$instance)
        {
            if (isset($_SERVER['REQUEST_METHOD']))
            {
                // Use the server request method
                Request::$method = $_SERVER['REQUEST_METHOD'];
            }

            if ( ! empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN))
            {
                // This request is secure
                Request::$protocol = 'https';
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            {
                // This request is an AJAX request
                Request::$is_ajax = TRUE;
            }

            if (isset($_SERVER['HTTP_REFERER']))
            {
                // There is a referrer for this request
                Request::$referrer = $_SERVER['HTTP_REFERER'];
            }

            if (isset($_SERVER['HTTP_USER_AGENT']))
            {
                // Set the client user agent
                Request::$user_agent = $_SERVER['HTTP_USER_AGENT'];
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                Request::$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                Request::$client_ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR']))
            {
                // The remote IP address
                Request::$client_ip = $_SERVER['REMOTE_ADDR'];
            }

            if (Request::$method !== 'GET' AND Request::$method !== 'POST')
            {
                // Methods besides GET and POST do not properly parse the form-encoded
                // query string into the $_POST array, so we overload it manually.
                parse_str(file_get_contents('php://input'), $_POST);
            }

            if ($uri === TRUE)
            {
                $uri = $_SERVER['PATH_INFO'];
            }

            // Reduce multiple slashes to a single slash
            $uri = preg_replace('#//+#', '/', $uri);

            // Remove all dot-paths from the URI, they are not valid
            $uri = preg_replace('#\.[\s./]*/#', '', $uri);

            $username = Request::$custom_domain_username = OrgDomainName::get_username_for_host($_SERVER['HTTP_HOST']);
            if ($username)
            {
                $uri = "$username$uri";
            }

            // Create the instance singleton
            Request::$instance = new Request($uri);

            // Add the default Content-Type header
            //Request::$instance->headers['Content-Type'] = 'text/html; charset='.Kohana::$charset;
        }

        return Request::$instance;
    }

    /**
     * Creates a new request object for the given URI. This differs from
     * [Request::instance] in that it does not automatically detect the URI
     * and should only be used for creating HMVC requests.
     *
     *     $request = Request::factory($uri);
     *
     * @param   string  URI of the request
     * @return  Request
     */
    public static function factory($uri)
    {
        return new Request($uri);
    }

    public static function is_post()
    {
        return Request::$method == "POST";
    }
    
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
     * @var  string  the URI of the request
     */
    public $uri;

    /**
     * Creates a new request object for the given URI. New requests should be
     * created using the [Request::instance] or [Request::factory] methods.
     *
     *     $request = new Request($uri);
     *
     * @param   string  URI of the request
     * @return  void
     * @throws  Kohana_Request_Exception
     * @uses    Route::all
     * @uses    Route::matches
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Returns the response as the string representation of a request.
     *
     *     echo $request;
     *
     * @return  string
     */
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

    public function rewrite_to_current_domain($url)
    {
        $username = Request::$custom_domain_username;
        if ($username)
        {
            $sitePrefix = Config::get('url') . $username;
            if (strpos($url, $sitePrefix) === 0)
            {
                $path = substr($url, strlen($sitePrefix));
                if (empty($path))
                {
                    $path = '/';
                }
                return "http://{$_SERVER['HTTP_HOST']}".$path;
            }
        }
        return $url;
    }

    public static function full_original_url()
    {
        $protocol = @$_SERVER['HTTPS'] ? "https://" : "http://";
        return "$protocol{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
    
    public static function canonical_url()
    {
        $canonical_url = Request::full_original_url();
        if (@$_GET['view'])
        {
            $canonical_url = url_with_param($canonical_url, 'view', null);
        }
        if (@$_GET['__sv'])
        {
            $canonical_url = url_with_param($canonical_url, '__sv', null);
        }
        return $canonical_url;
    }

    public function full_rewritten_url()
    {
        $protocol = @$_SERVER['HTTPS'] ? "https://" : "http://";
        $domain = Config::get('domain');
        $uri = $this->uri;
        $queryString = ($_SERVER['QUERY_STRING']) ? "?{$_SERVER['QUERY_STRING']}" : '';
        
        return "$protocol$domain/$uri$queryString";
    }

} // End Request
