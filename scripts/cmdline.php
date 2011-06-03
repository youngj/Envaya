<?php

/*
 * Provides utility functions for running scripts on the command line,
 * and ensures that scripts cannot be accessed via a browser.
 */

if (@$_SERVER['REQUEST_URI'])
{
    echo "This process must be run on the command line.";
    die;
}

function run_task($cmd, $cwd = null)
{
    print_msg($cmd);

    $descriptorspec = array(
       0 => array("pipe", "r"), // stdin is a pipe that the child will read from
       1 => STDOUT,
       2 => STDERR
    );
    return proc_open($cmd, $descriptorspec, $pipes, $cwd);
}

function print_msg($msg)
{
    echo time();
    echo " : ";
    echo $msg;
    echo "\n";
}

// readline from http://us3.php.net/manual/en/function.readline.php#49937
function _readline($prompt="") {
    echo $prompt;
    $o = "";
    $c = "";
    while ($c!="\r"&&$c!="\n") {
        $o.= $c;
        $c = fread(STDIN, 1);
    }
    fgetc(STDIN);
    return $o;
}

function prompt_default($prompt, $default)
{
    return _readline("$prompt [$default]") ?: $default;
}

if (!function_exists('pcntl_signal'))
{
    // stubs for windows
    function pcntl_signal($a,$b) {}
    function pcntl_signal_dispatch() {}
    function posix_setpgid($a,$b) {}
    define('SIGTERM', 0);
}
