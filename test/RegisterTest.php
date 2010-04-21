<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Selenium.php';
require_once 'PHPUnit/Framework.php';

class RegisterTest extends PHPUnit_Framework_TestCase
{
    private $s;

    public function setUp()
    {
        $this->s = new Testing_Selenium("*firefox", "http://localhost");
        $this->s->start();
        $this->s->windowMaximize();
    }

    public function tearDown()
    {
        $this->s->stop();
    }
    
    public function submitForm()
    {
        $this->s->click("//button");
        $this->s->waitForPageToLoad(10000);
    }

    public function testGoogle()
    {
        $this->s->open("/page/home");
        $this->s->click("//a[contains(@href,'org/new')]");        
        $this->s->waitForPageToLoad(10000);
        $this->s->click("//input[@name='org_type' and @value='p']");
        $this->submitForm();
        $this->s->click("//input[@name='org_type' and @value='np']");
        $this->submitForm();
        $this->s->mouseOver("//div[@class='bad_messages']");
        $this->s->click("//input[@name='country' and @value='other']");
        $this->submitForm();
        $this->s->mouseOver("//div[@class='bad_messages']");        
        $this->s->click("//input[@name='country' and @value='tz']");        
        $this->s->check("//input[@name='org_info[]' and @value='citizen']");
        $this->submitForm();
        $this->s->mouseOver("//div[@class='good_messages']");
        sleep(5);
    }

}


?>
