<?php

/*
 Command-line script for running Envaya's Selenium tests.
 
 usage: php TestSuite.php
    --test=TestClass1 --test=TestClass2 (from testcases directory, omit to run all test cases)
    --browser=*browser (selenium browser expression, omit to use *firefox)
    
    Assumes that http://localhost is running Envaya code, and no other Envaya services are running on localhost.    
    
    In Firefox, tries to run tests with Flash disabled.
    
    Firefox does not make it easy to programmatically disable plugins like Flash.
    On Windows, the path to Flash's DLL in test/profiles/noflash/pluginreg.dat must be exactly the same (case sensitive) as:
        registry key: HKEY_LOCAL_MACHINE\SOFTWARE\MozillaPlugins\@adobe.com/FlashPlayer ; 
        value name: Path    
*/

chdir(__DIR__);

require_once '../scripts/cmdline.php';
require_once 'PHPUnit/Autoload.php';
require_once 'SeleniumTest.php';

$MOCK_MAIL_FILE = __DIR__."/mail.out";

function get_all_test_cases()
{
    $paths = glob("{testcases/*.php,../mod/*/testcases/*.php}", GLOB_BRACE);
    return array_map(function($path) { 
        $pathinfo = pathinfo($path);
        return $pathinfo['filename'];
    }, $paths);
}

function find_test_case_path($test_case)
{
    $paths = glob("{testcases/$test_case.php,../mod/*/testcases/$test_case.php}", GLOB_BRACE);
    if (sizeof($paths))
    {
        return $paths[0];
    }
    throw new Exception("$test_case not found");
}

function check_selenium()
{
    $handle = @fsockopen("localhost", 4444);
    if (!$handle) { 
         echo "waiting for selenium server to respond...\n";
         sleep(1);  
         throw new Exception("selenium server not responding"); 
    }
    fclose($handle);
}

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
        $test_cases = get_all_test_cases();    
    }

    $suite = new PHPUnit_Framework_TestSuite('Envaya');

    foreach ($test_cases as $test_case)
    {
        require_once find_test_case_path($test_case);
        $suite->addTestSuite($test_case);
    }

    $descriptorspec = array(
       0 => array("pipe", "r"),
       1 => array("file", "selenium.out", 'w'),
       2 => STDERR
    );

    $selenium = proc_open('java -jar selenium-server.jar -singleWindow -firefoxProfileTemplate profiles/noflash', $descriptorspec, $pipes, __DIR__);

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

    retry('check_selenium', array());

    sleep(2);

    PHPUnit_TextUI_TestRunner::run($suite);
}

main(); 