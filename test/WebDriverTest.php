<?php

class WebDriverTest extends SeleniumTest
{
    function startBrowser() {
        global $BROWSER;

        $this->webdriver = new WebDriver("localhost", 4444);
        
        $this->webdriver->connect(array(
            'browserName' => $BROWSER,
        ));        
    }

    function tearDown() {
        $this->webdriver->close();
    }
    
    function open($url)
    {    
        if (strpos($url, '://') === false)
        {
            $url = "http://{$this->config['domain']}{$url}";
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
        return $this->retry('xpath', array($xpath), $timeout);
    }
    
    function waitForPageToLoad($timeout = 10000)
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
    
    function selectFrame($xpath)
    {
        if ($xpath == null)
        {
            $this->webdriver->selectFrame(null);
        }
        else
        {    
            $element = $this->xpath($xpath);                
            $id = $element->getAttribute('id');
            $this->webdriver->selectFrame($id);
        }
    }
    
    function mouseOver($xpath)
    {
        $this->xpath($xpath);
    }
    
    public function setTinymceContent($text)
    {
        //$this->retry('executeScript', array('window.tinymce.activeEditor.setContent('.json_encode($text).')',''));        

        $element = $this->selectFrame("//iframe");        
        $this->type("//body", $text);                
        $this->webdriver->selectFrame(null);
    }
    
    function mustExist($xpath)
    {
        $this->xpath($xpath);
    }
    
    function isElementPresent($xpath)
    {
        try
        {
            $this->xpath($xpath);
            return true;
        }
        catch (NoSuchElementException $ex)
        {
            return false;
        }
    }
    
    function ensureGoodMessage($msg = '')
    {
        $elem = $this->xpath("//div[@class='good_messages']");
        if ($msg)
        {
            $this->assertContains($msg, $elem->getText());
        }
    }    
    
    function ensureBadMessage($msg='')
    {
        $elem = $this->xpath("//div[@class='bad_messages']");
        if ($msg)
        {
            $this->assertContains($msg, $elem->getText());        
        }
    }        
    
    function isVisible($xpath)
    {
        $element = $this->xpath($xpath);
        return $element->isDisplayed();        
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
        
    function attachFile($xpath, $file)
    {
        $this->waitForElement($xpath);
        $this->type($xpath,str_replace("/","\\", $file));
    }
    
    public function submitForm($button = "//button[@type='submit']")
    {
        $this->waitForElement($button)->click();
    }
    
    function getValue($xpath)
    {
        return $this->xpath($xpath)->getValue();
    }
    
    function clear($xpath)
    {
        return $this->xpath($xpath)->clear();
    }

    function select($xpath)
    {
        return $this->xpath($xpath)->setSelected();
    }

    function toggle($xpath)
    {
        return $this->xpath($xpath)->toggle();
    }    
    
    function getSelectedLabel($xpath)
    {
        $select = $this->xpath($xpath);
        $value = $select->getValue();
        $option = $select->findElementBy(LocatorStrategy::xpath, "//option[@value='$value']");
        return $option->getAttribute('label');
    }
}