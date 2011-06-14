<?php

/*
 * Instantiates a standalone HTTP server for Envaya.
 * Just run it on the command line like "php web_server.php".
 */

include __DIR__ . '/webserver/webserver.php';
include dirname(__DIR__) . '/engine/config.php';

Config::load();

// override config settings for child process
$_ENV['ENVAYA_CONFIG'] = json_encode(array(
    'ssl_enabled' => false,
));

$domain = Config::get('domain');
$domain_parts = explode(':', $domain, 2);
$port = isset($domain_parts[1]) ? ((int)$domain_parts[1]) : 80;

$server = new WebServer(array(
    'port' => $port,
    'document_root' => Config::get('root') . '/www', 
    'static_regexes' => array(
        '#^/_media/#'
    ),
    'php_regexes' => array(
        '#\w+\.php#',
    ),
    'php_index' => '/index.php'
));

$server->run_forever();
