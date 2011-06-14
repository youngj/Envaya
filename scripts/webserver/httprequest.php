<?php

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
            
    /* 
     * Reads an entire HTTP request from a client socket, setting 
     * headers, content, request_uri, method, and http_version properties
     */
    function read_from_socket($client)
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
        
        $parsed_uri = parse_url($this->request_uri);        
        $this->uri = $parsed_uri['path'];
        $this->query_string = @$parsed_uri['query'];              
                
        // parse HTTP headers
        $start_headers = $end_req + 2;
                
        $headers_str = substr($request, $start_headers, $end_headers - $start_headers);
        $this->headers = $headers = WebServer::parse_headers($headers_str);
                    
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
}