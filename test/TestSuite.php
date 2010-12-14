<?php

require_once '../scripts/cmdline.php';

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'RegisterTest.php';

function main()
{
    global $BROWSER;

    $opts = getopt('',array("browser:"));
    $BROWSER = @$opts['browser'] ?: '*firefox';

    $suite = new PHPUnit_Framework_TestSuite('Envaya');

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

    $mockMailFile = __DIR__."/mail.out";

    @unlink($mockMailFile);
    
    $env = $_ENV;    
    $env["ENVAYA_CONFIG"] = json_encode(array(
        'mock_mail_file' => $mockMailFile
    ));
        
    $queue = proc_open('php runserver.php', $descriptorspec, $pipes2, dirname(__DIR__), $env);

    sleep(2);

    PHPUnit_TextUI_TestRunner::run($suite);
}

main();