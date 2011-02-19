<?php

/*
 Command-line script for running Envaya's Selenium tests.
 
 usage: php TestSuite.php
    --test=TestClass1 --test=TestClass2 (from testcases directory, omit to run all $TEST_CASES)
    --browser=*browser (selenium browser expression, omit to use *firefox)
    
    Assumes that http://localhost is running Envaya code, and no other Envaya services are running on localhost.
*/

$TEST_CASES = array(
    'MobileTest',
    'UploadTest',    
    'ReportingTest',    
    'FeedTest',
    'EnvayaSiteTest',
    'RegisterTest',
);

$MOCK_MAIL_FILE = __DIR__."/mail.out";

chdir(__DIR__);

require_once '../scripts/cmdline.php';
require_once 'PHPUnit/Autoload.php';
require_once 'SeleniumTest.php';

function main()
{
    global $BROWSER, $TEST_CASES, $MOCK_MAIL_FILE;

    $opts = getopt('',array("browser:","test:"));
        
    $BROWSER = @$opts['browser'] ?: '*firefox';

    if (@$opts['test'])
    {
        $test_cases = is_array($opts['test']) ? $opts['test'] : array($opts['test']);
    }
    else
    {    
        $test_cases = $TEST_CASES;    
    }

    $suite = new PHPUnit_Framework_TestSuite('Envaya');

    foreach ($test_cases as $test_case)
    {
        require_once("testcases/$test_case.php");
        $suite->addTestSuite($test_case);
    }

    $descriptorspec = array(
       0 => array("pipe", "r"),
       1 => array("file", "selenium.out", 'w'),
       2 => STDERR
    );

    $selenium = proc_open('java -jar selenium-server.jar -firefoxProfileTemplate profiles/noflash', $descriptorspec, $pipes, __DIR__);

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

    sleep(5);

    PHPUnit_TextUI_TestRunner::run($suite);
}

main(); 