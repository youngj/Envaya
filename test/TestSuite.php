<?php

$TEST_CASES = array(
    'ReportingTest',
    'MobileTest',
    'EnvayaSiteTest',
    'RegisterTest',
);

$MOCK_MAIL_FILE = __DIR__."/mail.out";

require_once '../scripts/cmdline.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'SeleniumTest.php';

function main()
{
    global $BROWSER, $TEST_CASES, $MOCK_MAIL_FILE;

    $opts = getopt('',array("browser:"));
    $BROWSER = @$opts['browser'] ?: '*firefox';

    $suite = new PHPUnit_Framework_TestSuite('Envaya');

    foreach ($TEST_CASES as $test_case)
    {
        require_once("testcases/$test_case.php");
        $suite->addTestSuite($test_case);
    }

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
    
    $env = $_ENV;    
    
    if (!sizeof($env))
    {
        echo "error: \$_ENV is empty. add variables_order=\"GPCSE\" to your php.ini file and try again.\n";
        die;
    }   
    
    $env["ENVAYA_CONFIG"] = json_encode(array(
        'mock_mail_file' => $MOCK_MAIL_FILE
    ));
        
    $queue = proc_open('php runserver.php', $descriptorspec, $pipes2, dirname(__DIR__), $env);

    sleep(2);

    PHPUnit_TextUI_TestRunner::run($suite);
}

main();