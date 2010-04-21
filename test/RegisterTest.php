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
        $this->clickAndWait("//button");
    }
    
    public function clickAndWait($selector)
    {
        $this->s->click($selector);        
        $this->s->waitForPageToLoad(10000);    
    }

    public function test()
    {
        $this->s->open("/");
                
        $this->clickAndWait("//a[contains(@href,'page/home')]");            

        $this->clickAndWait("//a[contains(@href,'org/new')]");        


        /* qualification */
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
        
        /* create account */
        
        $this->s->mouseOver("//div[@class='good_messages']");
        
        $this->s->type("//input[@name='org_name']", "testorg");
        $this->s->type("//input[@name='username']", "t<b>x</b>");
        $this->s->type("//input[@name='password']", "password");
        $this->s->type("//input[@name='password2']", "password2");        
        $this->s->type("//input[@name='email']", "adunar@gmail.com");
        $this->submitForm();        
        $this->s->mouseOver("//div[@class='bad_messages']");        

        $this->s->type("//input[@name='username']", "test".time());
        $this->submitForm();
        $this->s->mouseOver("//div[@class='bad_messages']");        

        $this->s->type("//input[@name='password2']", "password");
        $this->s->type("//input[@name='username']", "org");
        $this->submitForm();
        $this->s->mouseOver("//div[@class='bad_messages']");        

        $this->s->type("//input[@name='username']", "test".time());
        $this->submitForm();                
        
        /* set up homepage */

        $this->s->mouseOver("//div[@class='good_messages']");   
        
        $this->s->type("//input[@name='mission']", "testing the website");
        $this->s->check("//input[@name='sector[]' and @value='3']");
        $this->s->check("//input[@name='sector[]' and @value='99']");
        $this->s->type("//input[@name='sector_other']", "another sector");
        $this->s->type("//input[@name='city']", "Wete");        
        $this->s->select("//select[@name='region']", "Pemba North");        
        
        $this->submitForm();

        /* home page */        
        
        $this->s->mouseOver("//div[@class='good_messages']");
        
        $this->s->mouseOver("//h2[text()='testorg']");
        $this->s->mouseOver("//h3[text()='Wete, Tanzania']");
        $this->s->mouseOver("//a[contains(@href,'org/browse?list=1&sector=3') and text()='Conflict resolution']");
    }
}


?>
