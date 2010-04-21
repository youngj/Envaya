<?php
 
require_once '../scripts/cmdline.php'; 
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
 
require_once 'RegisterTest.php';
 
$suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
 
$suite->addTestSuite('RegisterTest');

$descriptorspec = array(
   0 => array("pipe", "r"), 
   1 => array("file", "selenium.out", 'w'),
   2 => STDERR
);
    
$selenium = proc_open('java -jar selenium-server.jar', $descriptorspec, $pipes);

$descriptorspec = array(
   0 => array("pipe", "r"), 
   1 => STDOUT, //array("file", "runserver.out", 'w'),
   2 => STDERR
);
    
$queue = proc_open('php runserver.php', $descriptorspec, $pipes2, dirname(dirname(__FILE__)));

sleep(2);

PHPUnit_TextUI_TestRunner::run($suite); 

print "DONE!";

