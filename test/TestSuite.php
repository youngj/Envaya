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

require_once dirname(__DIR__).'/scripts/cmdline.php';
require_once dirname(__DIR__).'/start.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/SeleniumTest.php';
require_once __DIR__.'/WebDriverTest.php';
require_once __DIR__.'/webdriver/phpwebdriver/WebDriver.php';

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__).'/vendors/zend');
require_once 'Zend/Loader.php';

Config::load();

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
    chdir(dirname(__DIR__));
    $modules = explode("\n", `php scripts/module_list.php`);
    $module_glob = "{".implode(",", $modules)."}";

    return glob("{test/testcases/$test_case.php,mod/$module_glob/test/testcases/$test_case.php}", GLOB_BRACE);
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

function stop_selenium()
{
    $sel = new Testing_Selenium("",""); 
    $sel->shutDownSeleniumServer();
}

class TestListener implements PHPUnit_Framework_TestListener
{
    private $is_first_test = true;

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) 
    {
        error_log($e->getTraceAsString());
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        error_log($e->getMessage());
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) 
    {
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {    
    }

    public function startTest(PHPUnit_Framework_Test $test) 
    {
        echo "\n".get_class($test)."\n";
        
        global $TEST_CONFIG;        
        @unlink($TEST_CONFIG['mail:mock_file']);    
        @unlink($TEST_CONFIG['sms:mock_file']);   
        
        if (!$this->is_first_test)
        {
			run_test_task_sync('php scripts/clear_queue.php', array('quiet' => true));
			usleep(500000);
            run_test_task_sync("php test/reset_db.php | mysql -u root", array('quiet' => true));
            run_test_task_sync("mysql envaya_test -u root < {$TEST_CONFIG['dataroot']}/test_init.sql", array('quiet' => true));
            run_test_task_sync("php scripts/reindex_sphinx.php", array('quiet' => true));            
        }
    }

    public function endTest(PHPUnit_Framework_Test $test, $time) 
    {
        $this->is_first_test = false;
    }
}

function get_test_environment()
{
    global $TEST_CONFIG;
    $env = get_environment();    
    $env["ENVAYA_CONFIG"] = json_encode($TEST_CONFIG);            
    return $env;
}

function run_test_task_sync($cmd, $options = null)
{
    return run_task_sync($cmd, dirname(__DIR__), get_test_environment(), $options);
}

function run_test_suite($suite, $opts)
{
    if (!isset($opts['selenium']))
    {
        $opts['selenium'] = true;
    }

    if (!isset($opts['runserver']))
    {
        $opts['runserver'] = true;
    }    
    
    if (!isset($opts['reset']))
    {
        $opts['reset'] = true;
    }
    
    global $BROWSER;
    $BROWSER = @$opts['browser'] ?: 'firefox';

    global $TEST_CONFIG;    
    $TEST_CONFIG = include __DIR__."/config.php";       
    
    $test_dataroot = $TEST_CONFIG['dataroot'];
    
    if ($opts['reset'])
    {    
        chdir(dirname($test_dataroot));
        system("rm -rf test_data");       
    }
    chdir(__DIR__);
    
    umask(0);
    @mkdir($test_dataroot, 0777, true);       
    
    if ($opts['selenium'])
    {
        require_once dirname(__DIR__)."/scripts/install_selenium.php";
    
        $descriptorspec = array(
           0 => array("pipe", "r"),
           1 => array("file", "$test_dataroot/selenium.out", 'w'),
           2 => array("file", "$test_dataroot/selenium.err.out", 'w')
        );
            
        $selenium_path = Config::get('dataroot') . "/" . Config::get('test:selenium_jar');    
        
        $selenium = proc_open("java -jar $selenium_path -singleWindow", 
            $descriptorspec, $pipes, __DIR__);  
    }
    
    $env = get_environment();    
    $env["ENVAYA_CONFIG"] = json_encode($TEST_CONFIG);            
    $root = dirname(__DIR__);
        
    if ($opts['reset'])
    {
        run_test_task_sync("php test/reset_db.php | mysql -u root");
        run_test_task_sync('php scripts/install_tables.php');    
        run_test_task_sync('php scripts/install_kestrel.php');
        run_test_task_sync('php scripts/install_sphinx.php');
        run_test_task_sync('php scripts/install_test_data.php');   
    }
    run_test_task_sync("mysqldump envaya_test -u root > $test_dataroot/test_init.sql");    
         
    if ($opts['runserver'])
    {     
        $descriptorspec = array(
           0 => array("pipe", "r"),
           1 => array("file", "$test_dataroot/runserver.out", 'w'),
           2 => STDERR
        );                
      
        $runserver = proc_open('php runserver.php', $descriptorspec, $runserver_pipes, 
            dirname(__DIR__), 
            get_test_environment());    
        
        $runserver_status = proc_get_status($runserver);
        posix_setpgid($runserver_status['pid'], $runserver_status['pid']);   
    }
    
    if ($opts['selenium'])
    {
        retry('check_selenium');
    }
    sleep(2);

    PHPUnit_TextUI_TestRunner::run($suite, array(
        'listeners' => array(
            new TestListener()
        ),
    ));
    
    if ($opts['runserver'])
    {    
        if (function_exists('posix_kill'))
        {    
            posix_kill(-$runserver_status['pid'], SIGTERM);
        }
        else
        {
            kill_windows_process_tree($runserver_status['pid']);
        }
    }
    
    if ($opts['selenium'])
    {
        stop_selenium();
    }
}

function main()
{    
    $opts = getopt('',array("browser:","test:",));
     
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
    
    run_test_suite($suite, $opts);
}

if (realpath($argv[0]) == __FILE__)
{    
    main(); 
}
