<?php

if (@$_SERVER['REQUEST_URI'])
{
    die("This process must be run on the command line.");
}

$descriptorspec = array(
   0 => array("pipe", "r"), // stdin is a pipe that the child will read from
   1 => STDOUT,
   2 => STDERR 
);

while (true)
{
    echo time();
    echo " spawning worker\n";
    $worker = proc_open("php worker.php", $descriptorspec, $pipes);

    if (!is_resource($worker))
    {       
        die("Error spawning worker");
    }
    
    while (true)
    {
        $status = proc_get_status($worker);
        if (!$status['running'])
        {
            break;
        }   
        sleep(1);   
    }
}