<?php

class BadRequestException extends Exception {}

class HTTPRequest
{
    const READ_HEADERS = 0;
    const READ_CONTENT = 1;
    const READ_COMPLETE = 2;

    const BUFFER_SIZE = 8092;

    public $method;             // HTTP method, e.g. "GET" or "POST"
    public $request_uri;        // original requested URI, with query string
    public $uri;                // path component of URI, without query string
    public $http_version;       // version from the request line, e.g. "HTTP/1.1"
    public $query_string;       // query string, like "a=b&c=d"
    public $headers;            // associative array of HTTP headers    
    public $content;            // content of POST request, if applicable    
            
    public $socket;
    
    private $cur_state = 0;
    private $header_buf = '';
    private $content_len = 0;
    
    public $response;
    public $response_buf;
    
    //public $leftover_data;
                        
    function __construct($socket)
    {
        $this->socket = $socket;
    }
              
    function set_response($response)
    {
        $this->response = $response;
        $this->response_buf = $response->render(); 
    }
              
    /* 
     * Reads an entire HTTP request from a client socket, setting 
     * headers, content, request_uri, method, and http_version properties
     */
    function add_data($data)
    {    
        switch ($this->cur_state)
        {
            case static::READ_HEADERS:
                $header_buf =& $this->header_buf;
            
                $header_buf .= $data;
                       
                $end_headers = strpos($header_buf, "\r\n\r\n");
                if ($end_headers === false)
                {
                    break;
                }         

                // parse HTTP request line    
                $end_req = strpos($header_buf, "\r\n"); 
                $req_line = substr($header_buf, 0, $end_req);
                $req_arr = explode(' ', $req_line, 3);

                $this->method = $req_arr[0];
                $this->request_uri = $req_arr[1];
                $this->http_version = $req_arr[2];    
                
                $parsed_uri = parse_url($this->request_uri);        
                $this->uri = $parsed_uri['path'];
                $this->query_string = @$parsed_uri['query'];              
                
                // parse HTTP headers
                $start_headers = $end_req + 2;
                        
                $headers_str = substr($header_buf, $start_headers, $end_headers - $start_headers);
                $this->headers = $headers = WebServer::parse_headers($headers_str);

                $this->content_len = (int)@$headers['Content-Length'];
                
                $start_content = $end_headers + 4; // $end_headers is before last \r\n\r\n
                
                // add leftover to content
                $this->add_content(substr($header_buf, $start_content));
                $header_buf = '';                                
                break;
            case static::READ_CONTENT:
                $this->add_content($data);
                break;
            case static::READ_COMPLETE:
                break;
        }    
        
        if (!$this->headers)
        {
            $this->cur_state = static::READ_HEADERS;
        }
        else if ($this->needs_content())
        {
            $this->cur_state = static::READ_CONTENT;
        }
        else
        {
            $this->cur_state = static::READ_COMPLETE;
        }
    }
    
    function add_content($data)
    {
        if ($this->content === null)
        {
            $this->content = $data;
        }
        else
        {
            $this->content .= $data;
        }
    }
    
    function is_read_complete()
    {
        return $this->cur_state == static::READ_COMPLETE;
    }
    
    function needs_content()
    {
        $rem_bytes = $this->content_len - strlen($this->content);
        return ($rem_bytes > 0);
    }            
}