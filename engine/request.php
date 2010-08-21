<?php
/**
 * Request and response wrapper. Uses the [Route] class to determine what
 * [Controller] to send the request to.
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

    /**
     * @var  object  currently executing request instance
     */
    public static $current;

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

            $username = Request::$custom_domain_username = get_username_for_host($_SERVER['HTTP_HOST']);
            if ($username)
            {
                $uri = "$username$uri";
            }

            // Create the instance singleton
            Request::$instance = Request::$current = new Request($uri);

            // Add the default Content-Type header
            //Request::$instance->headers['Content-Type'] = 'text/html; charset='.Kohana::$charset;
        }

        return Request::$instance;
    }

    /**
     * Return the currently executing request. This is changed to the current
     * request when [Request::execute] is called and restored when the request
     * is completed.
     *
     *     $request = Request::current();
     *
     * @return  Request
     * @since   3.0.5
     */
    public static function current()
    {
        return Request::$current;
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

    /**
     * @var  object  route matched for this request
     */
    public $route;

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
     * @var  string  controller directory
     */
    public $directory = '';

    /**
     * @var  string  controller to be executed
     */
    public $controller;

    /**
     * @var  string  action to be executed in the controller
     */
    public $action;

    /**
     * @var  string  the URI of the request
     */
    public $uri;

    // Parameters extracted from the route
    protected $_params;

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
        // Remove trailing slashes from the URI
        $uri = trim($uri, '/');

        // Load routes
        $routes = Route::all();

        foreach ($routes as $name => $route)
        {
            if ($params = $route->matches($uri))
            {
                // Store the URI
                $this->uri = $uri;

                // Store the matching route
                $this->route = $route;

                if (isset($params['directory']))
                {
                    // Controllers are in a sub-directory
                    $this->directory = $params['directory'];
                }

                // Store the controller
                $this->controller = $params['controller'];

                if (isset($params['action']))
                {
                    // Store the action
                    $this->action = $params['action'];
                }
                else
                {
                    // Use the default action
                    $this->action = Route::$default_action;
                }

                // These are accessible as public vars and can be overloaded
                unset($params['controller'], $params['action'], $params['directory']);

                // Params cannot be changed once matched
                $this->_params = $params;

                return;
            }
        }

        // No matching route for this URI
        $this->status = 404;
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
     * Generates a relative URI for the current route.
     *
     *     $request->uri($params);
     *
     * @param   array   additional route parameters
     * @return  string
     * @uses    Route::uri
     */
    public function uri(array $params = NULL)
    {
        if ( ! isset($params['directory']))
        {
            // Add the current directory
            $params['directory'] = $this->directory;
        }

        if ( ! isset($params['controller']))
        {
            // Add the current controller
            $params['controller'] = $this->controller;
        }

        if ( ! isset($params['action']))
        {
            // Add the current action
            $params['action'] = $this->action;
        }

        // Add the current parameters
        $params += $this->_params;

        return $this->route->uri($params);
    }

    /**
     * Create a URL from the current request. This is a shortcut for:
     *
     *     echo URL::site($this->request->uri($params), $protocol);
     *
     * @param   string   route name
     * @param   array    URI parameters
     * @param   mixed    protocol string or boolean, adds protocol and domain
     * @return  string
     * @since   3.0.7
     * @uses    URL::site
     */
    public function url(array $params = NULL, $protocol = NULL)
    {
        // Create a URI with the current route and convert it to a URL
        return URL::site($this->uri($params), $protocol);
    }

    /**
     * Retrieves a value from the route parameters.
     *
     *     $id = $request->param('id');
     *
     * @param   string   key of the value
     * @param   mixed    default value if the key is not set
     * @return  mixed
     */
    public function param($key = NULL, $default = NULL)
    {
        if ($key === NULL)
        {
            // Return the full array
            return $this->_params;
        }

        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
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

    /**
     * Redirects as the request response. If the URL does not include a
     * protocol, it will be converted into a complete URL.
     *
     *     $request->redirect($url);
     *
     * [!!] No further processing can be done after this method is called!
     *
     * @param   string   redirect location
     * @param   integer  status code: 301, 302, etc
     * @return  void
     * @uses    URL::site
     * @uses    Request::send_headers
     */
    public function redirect($url, $code = 302)
    {
        if (strpos($url, '://') === FALSE)
        {
            // Make the URI into a URL
            $url = URL::site($url, TRUE);
        }

        // Set the response status
        $this->status = $code;

        // Set the location header
        $this->headers['Location'] = $url;

        // Send headers
        $this->send_headers();

        // Stop execution
        exit;
    }


    /**
     * Processes the request, executing the controller action that handles this
     * request, determined by the [Route].
     *
     * 1. Before the controller action is called, the [Controller::before] method
     * will be called.
     * 2. Next the controller action will be called.
     * 3. After the controller action is called, the [Controller::after] method
     * will be called.
     *
     * By default, the output from the controller is captured and returned, and
     * no headers are sent.
     *
     *     $request->execute();
     *
     * @return  $this
     * @throws  Kohana_Exception
     * @uses    [Kohana::$profiling]
     * @uses    [Profiler]
     */
    public function execute()
    {
        if ($this->status == 404)
        {
            not_found();
        }

        // Create the class prefix
        $prefix = 'controller_';

        if ($this->directory)
        {
            // Add the directory name to the class prefix
            $prefix .= str_replace(array('\\', '/'), '_', trim($this->directory, '/')).'_';
        }

        // Store the currently active request
        $previous = Request::$current;

        // Change the current request to this request
        Request::$current = $this;
        
        try
        {
            // Load the controller using reflection
            $class = new ReflectionClass($prefix.$this->controller);
         
            if ($class->isAbstract())
            {
                throw new Kohana_Exception('Cannot create instances of abstract :controller',
                    array(':controller' => $prefix.$this->controller));
            }

            // Create a new instance of the controller
            $controller = $class->newInstance($this);

            // Execute the "before action" method
            $class->getMethod('before')->invoke($controller);

            // Determine the action to use
            $action = empty($this->action) ? Route::$default_action : $this->action;

            // Execute the main action with the parameters
            $class->getMethod('action_'.$action)->invokeArgs($controller, $this->_params);

            // Execute the "after action" method
            $class->getMethod('after')->invoke($controller);
        }
        catch (Exception $e)
        {
            // Restore the previous request
            Request::$current = $previous;

            if ($e instanceof ReflectionException)
            {
                // Reflection will throw exceptions for missing classes or actions
                $this->status = 404;
                not_found();
            }
            else
            {
                // All other exceptions are PHP/server errors
                $this->status = 500;
            }

            // Re-throw the exception
            throw $e;
        }

        // Restore the previous request
        Request::$current = $previous;

        return $this;
    }

    public function rewrite_to_current_domain($url)
    {
        $username = Request::$custom_domain_username;
        if ($username)
        {
            global $CONFIG;
            $sitePrefix = $CONFIG->url . $username;
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

    public function full_original_url()
    {
        $protocol = @$_SERVER['HTTPS'] ? "https://" : "http://";
        return "$protocol{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }

    public function full_rewritten_url()
    {
        global $CONFIG;
        $protocol = @$_SERVER['HTTPS'] ? "https://" : "http://";
        $uri = Request::instance()->uri;
        $queryString = ($_SERVER['QUERY_STRING']) ? "?{$_SERVER['QUERY_STRING']}" : '';
        return "$protocol{$CONFIG->domain}/$uri$queryString";
    }

} // End Request
