<?php

if (@$_SERVER['REQUEST_URI'])
{
    die("This process must be run on the command line.");
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