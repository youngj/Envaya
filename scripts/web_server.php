<?php

/*
 * Instantiates a standalone HTTP server for Envaya.
 * Just run it on the command line like "php web_server.php".
 */

require_once __DIR__ . '/httpserver/httpserver.php';
require_once dirname(__DIR__) . '/start.php';

class EnvayaHTTPServer extends HTTPServer
{
    function __construct()
    {
        $domain = Config::get('domain');
        list($host, $port) = explode(':', $domain, 2);
        $port = ((int)$port) ?: 80;
        
        parent::__construct(array(
            'port' => $port,
            'cgi_env' => array(
                'ENVAYA_CONFIG' => getenv('ENVAYA_CONFIG')
            )
        ));
    }

    function route_request($request)
    {
        $uri = $request->uri;
        
        $doc_root = dirname(__DIR__) . '/www';
        
        if (preg_match('#^/_media/#', $uri))
        {
            return $this->get_static_response($request, "$doc_root$uri");
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

$server = new EnvayaHTTPServer();
$server->run_forever();