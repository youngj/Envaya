<?php
 
require_once "scripts/cmdline.php";
require_once "engine/start.php";

Config::set('debug', false);

$uri = @$argv[1] ?: "/";

$end_init_mtime = microtime(true);

$t_init = ($end_init_mtime - Engine::$init_microtime);

function exec_request($uri)
{
    $start_mtime = microtime(true);        
    $request = new Request($uri, array('host' => 'localhost'));
    $controller = new Controller_Default($request);
    $controller->execute($request->uri);    
    $finish_mtime = microtime(true);
    
    return array(
        'time' => ($finish_mtime - $start_mtime),
        'request' => $request
    );
}

$res = exec_request($uri);

echo "$uri (len=".(strlen($res['request']->response)).")\n";
echo " t_init  = $t_init\n";
echo " t_exec  = {$res['time']}\n";    
echo " t_total = ".($t_init + $res['time'])."\n";