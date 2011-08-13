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

function run_task($cmd, $cwd = null, $env = null)
{
    print_msg($cmd);
    
    $descriptorspec = array(
       0 => array("pipe", "r"), // stdin is a pipe that the child will read from
       1 => STDOUT,
       2 => STDERR
    );
    return proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
}

function run_task_sync($cmd, $cwd = null, $env = null)
{
    proc_close(run_task($cmd, $cwd, $env));
}

function print_msg($msg)
{
    echo time();
    echo " : ";
    echo $msg;
    echo "\n";
}

if (!function_exists('readline'))
{
    // readline for windows from http://us3.php.net/manual/en/function.readline.php#49937
    function readline($prompt="") {
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
}

function get_environment()
{
    // provide some required/useful environment variables even if 'E' is not in variables_order
    $env_keys = array('HOME','OS','Path','PATHEXT','SystemRoot','TEMP','TMP');
    foreach ($env_keys as $key)
    {
        $_ENV[$key] = getenv($key);
    }        
    return $_ENV;    
}

function prompt_default($prompt, $default)
{
    return readline("$prompt [$default]") ?: $default;
}

function render_config_template($src_file, $dest_file)
{
    $conf_template = file_get_contents($src_file);

    $replacements = array();
    foreach (Config::get_all() as $k => $v)
    {
        $replacements["{{".$k."}}"] = $v;
    }

    $conf = strtr($conf_template, $replacements);

    if (file_put_contents($dest_file, $conf))
    {
        error_log("Wrote $dest_file");
        return true;
    }
    else
    {
        error_log("Error writing $dest_file");
    }
}

// stubs for windows
if (!function_exists('posix_setpgid'))
{
    function posix_setpgid($a,$b) {}
}

if (!function_exists('pcntl_signal'))
{   
    function pcntl_signal($a,$b) {}
    function pcntl_signal_dispatch() {}
    define('SIGTERM', 0);
    define('SIGINT', 0);
}
