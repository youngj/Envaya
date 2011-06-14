<?php

/*
 * Encapsulates information about the response to a HTTP request (i.e., the output
 * of a controller action within Envaya).
 */
class Response 
{
    /**
     * @var  integer  HTTP response code: 200, 404, 500, etc
     */
    public $status;

    /**
     * @var  string  response body
     */
    public $content;

    /**
     * @var  array  headers to send with the response body
     */
    public $headers;

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
    
    function __construct($status = 200, $content = '', $headers = null)
    {
        $this->status = $status;
        $this->content = $content;
        $this->headers = $headers ?: array();
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
    function send_headers()
    {
        if (!headers_sent())
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
            $status = $this->status;
            header($protocol.' '.$status.' '.static::$messages[$status]);

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
}