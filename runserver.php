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

echo "spawning kestrel\n";
$kestrel = proc_open("java -jar kestrel-1.2.jar -f kestrel.conf", $descriptorspec, $pipes, dirname(__FILE__)."/vendors/kestrel_dev");
sleep(3);
echo "spawning queue runner\n";
$queueRunner = proc_open("php queueRunner.php", $descriptorspec, $pipes2);
