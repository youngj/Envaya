<?php
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
 
require_once 'RegisterTest.php';
 
$suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
 
$suite->addTestSuite('RegisterTest');

PHPUnit_TextUI_TestRunner::run($suite); 
