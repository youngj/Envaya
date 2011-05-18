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
     * @var  string the URI of the request
     */
    public $uri;    
    
    public $host;
    
    public $query_string;
    
    /**
     * Main request singleton instance, with parameters determined from the HTTP request.
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

    public function __construct($uri, $options = null)
    {
        $this->uri = $uri;
        
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
        return "{$this->protocol}://{$this->host}{$this->uri}{$this->query_string}";
    }
    
    public function is_post()
    {
        return $this->method == "POST";
    }
    
    public function is_secure()
    {
        return $this->protocol == 'https';
    }   

    function is_mobile_browser()
    {
        $useragent = $this->user_agent;
        
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent)
         || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent,0,4)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }        
}
