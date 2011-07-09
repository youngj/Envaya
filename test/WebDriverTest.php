<?php

class WebDriverTest extends SeleniumTest
{
    function setUp() {
        global $BROWSER;
    
        $this->webdriver = new WebDriver("localhost", 4444);
        $this->webdriver->connect($BROWSER);
    }

    function tearDown() {
        $this->webdriver->close();
    }
    
    function open($url)
    {
        global $DOMAIN;
    
        if (strpos($url, '://') === false)
        {
            $url = "http://{$DOMAIN}{$url}";
        }
        
        $this->webdriver->get($url);
    }

    function xpath($xpath)
    {
        $element = $this->webdriver->findElementBy(LocatorStrategy::xpath, $xpath);
        $this->assertNotNull($element);
        return $element;
    }
    
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->webdriver, $name), $arguments);
    }    
    
    function waitForElement($xpath, $timeout = 15)
    {
        $this->retry('xpath', array($xpath), $timeout);
    }
    
    function waitForPageToLoad($timeout)
    {
    }   
    
    function type($xpath, $text)
    {
        $element = $this->xpath($xpath);
        $element->sendKeys(array($text));
    }
    
    function click($xpath)
    {
        $element = $this->xpath($xpath);   
        $element->click();
    }
    
    function typeInFrame($xpath, $text)
    {        
        $element = $this->xpath($xpath);        
        
        $id = $element->getAttribute('id');
        $this->webdriver->selectFrame($id);
        
        $this->type("//body", $text);        
        
        $this->webdriver->selectFrame(null);
    }
    
    function mustExist($xpath)
    {
        $this->xpath($xpath);
    }
    
    function ensureGoodMessage($msg)
    {
        $elem = $this->xpath("//div[@class='good_messages']");
        $this->assertContains($msg, $elem->getText());
    }    
    
    function ensureBadMessage($msg)
    {
        $elem = $this->xpath("//div[@class='bad_messages']");
        $this->assertContains($msg, $elem->getText());        
    }        
    
    function getText($xpath)
    {
        $element = $this->xpath($xpath);
        return $element->getText();
    }

    function getLocation()
    {
        return $this->getCurrentUrl();
    }
    
    function logout()
    {
        $this->click("//a[contains(@href,'pg/logout')]");    
        $this->retry('click', array("//a[@id='loginButton']"));    
    }
}