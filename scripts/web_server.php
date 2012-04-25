<?php

/*
 * Instantiates a standalone HTTP server for development.
 * Just run it on the command line like "php web_server.php".
 */

require_once __DIR__ . '/httpserver/httpserver.php';
require_once dirname(__DIR__) . '/start.php';

class AppHTTPServer extends HTTPServer
{
    function __construct()
    {
        $domain = Config::get('domain');
        list($host, $port) = explode(':', $domain, 2);
        $port = ((int)$port) ?: 80;
        
        parent::__construct(array(
            'port' => $port,
            'cgi_env' => array(
                'APP_CONFIG' => getenv('APP_CONFIG')
            )
        ));
    }

    function route_request($request)
    {
        $uri = $request->uri;
        
        $doc_root = dirname(__DIR__) . '/www';
        
        if (preg_match('#^/_media/#', $uri))
        {
            $local_path = "$doc_root$uri";
        
            $accept_encoding = $request->get_header('Accept-Encoding');
            if ($accept_encoding && strpos($accept_encoding, 'gzip') !== false)
            {            
                $mime_type = static::get_mime_type($local_path);            
                if ($mime_type == 'application/x-javascript'
                 || $mime_type == 'text/css') 
                {
                    // simulate nginx gzip_static                 
                    $gz_path = "$local_path.gz";
                    if (is_file($gz_path))
                    {
                        return $this->response(200, 
                            fopen($gz_path, 'rb'), 
                            array(
                                'Content-Type' => $mime_type,
                                'Cache-Control' => "max-age=8640000",
                                'Content-Encoding' => 'gzip',
                                'Content-Length' => filesize($gz_path), 
                            )
                        );                
                    }
                }
            }
            
            return $this->get_static_response($request, $local_path);
        }        
        else if (preg_match('#^/\w+\.php$#', $uri))
        {
            return $this->get_php_response($request, "$doc_root$uri");
        }
        else
        {
            return $this->get_php_response($request, "$doc_root/index.php", array(
                'PATH_INFO' => $uri
            ));
        }
    }
    
    function bind_error($errno, $errstr)
    {
        parent::bind_error($errno, $errstr);
        error_log("If you're running this application on another web server, ignore this message.\n"
            ."Otherwise, stop the existing server or add a port to your 'domain'.\n"
            ." e.g. add  'domain' => 'localhost:####',  in config/local.php");    
    }
}

$server = new AppHTTPServer();
$server->run_forever();