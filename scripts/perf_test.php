<?php

$start_microtime = microtime(true);
 
require_once "scripts/cmdline.php";
require_once "start.php";

Config::set('debug', false);

$uri = @$argv[1] ?: "/";

$end_init_mtime = microtime(true);

$t_init = ($end_init_mtime - $start_microtime);

function exec_request($uri)
{
    $start_mtime = microtime(true);        
    $controller = new Controller_Default();
    $controller->execute($uri);    
    $finish_mtime = microtime(true);
    
    return array(
        'time' => ($finish_mtime - $start_mtime),
        'response' => $controller->get_response()
    );
}

$res = exec_request($uri);

echo "$uri (len=".strlen($res['response']->content).")\n";
echo " t_init  = $t_init\n";
echo " t_exec  = {$res['time']}\n";    
echo " t_total = ".($t_init + $res['time'])."\n";