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
require_once '../engine/config.php';
require_once 'PHPUnit/Autoload.php';
require_once 'SeleniumTest.php';

Config::load();

$MOCK_MAIL_FILE = __DIR__."/mail.out";
$DOMAIN = Config::get('domain');

function kill_windows_process_tree($pid, $wmi = null)
{
    //echo "kill $pid\n";

    if ($wmi == null) 
    {
        $wmi = new COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2"); 
    }

    // adapted from http://www.php.net/manual/en/function.posix-kill.php#102094
    
    $procs = $wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId='$pid'");     
    $children = $wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ParentProcessId='$pid'"); 
    
    foreach ($procs as $proc)
    {
        $proc->Terminate();
        break;
    }
    
    foreach ($children as $child) 
    {
        kill_windows_process_tree($child->ProcessId, $wmi);
    }
}

function get_all_test_cases()
{
    // each php file in test/testcases/ is assumed to be a test class with the same name as the file.
    $paths = get_test_case_paths();
    
    return array_map(function($path) { 
        $pathinfo = pathinfo($path);
        return $pathinfo['filename'];
    }, $paths);
}

function get_test_case_paths($test_case = "*")
{    
    // look in test/testcases/ in all the enabled modules
    $modules = explode("\n", `php ../scripts/module_list.php`);
    $module_glob = "{".implode(",", $modules)."}";

    return glob("{testcases/$test_case.php,../mod/$module_glob/test/testcases/$test_case.php}", GLOB_BRACE);
}

function get_test_case_path($test_case)
{        
    $paths = get_test_case_paths($test_case);
    if (sizeof($paths) > 0)
    {
        return $paths[0];
    }
    throw new Exception("$test_case not found");
}

function check_selenium()
{
    $handle = @fsockopen("localhost", 4444, $errno, $errstr, 1);
    if (!$handle) { 
         echo "waiting for selenium server to respond...\n";
         sleep(1);  
         throw new Exception("selenium server not responding"); 
    }
    fclose($handle);
}

function main()
{
    global $BROWSER, $TEST_CASES, $MOCK_MAIL_FILE, $DOMAIN;

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
        require_once get_test_case_path($test_case);
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
    
    // provide some required/useful environment variables even if 'E' is not in variables_order
    $env_keys = array('HOME','OS','Path','PATHEXT','SystemRoot','TEMP','TMP');
    foreach ($env_keys as $key)
    {
        $_ENV[$key] = getenv($key);
    }    
    
    $env = $_ENV;    
    
    $env["ENVAYA_CONFIG"] = json_encode(array(        
        'captcha_enabled' => false,
        'ssl_enabled' => false,
        'mock_mail_file' => $MOCK_MAIL_FILE
    ));
        
    $runserver = proc_open('php runserver.php', $descriptorspec, $pipes2, dirname(__DIR__), $env);
    $status = proc_get_status($runserver);                
    posix_setpgid($status['pid'], $status['pid']);   
    
    retry('check_selenium');

    sleep(2);

    PHPUnit_TextUI_TestRunner::run($suite);
    
    if (function_exists('posix_kill'))
    {
        posix_kill(-$status['pid'], SIGTERM);
    }
    else
    {
        kill_windows_process_tree($status['pid']);
    }

    proc_terminate($selenium);
}

main(); 
