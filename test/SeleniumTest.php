<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Selenium.php';
require_once 'PHPUnit/Framework.php';

class SeleniumTest extends PHPUnit_Framework_TestCase
{
    protected $s;
    
    public function setUp()
    {
        global $BROWSER, $MOCK_MAIL_FILE;
        
        @unlink($MOCK_MAIL_FILE);

        $this->s = new Testing_Selenium($BROWSER, "http://localhost");
        $this->s->start();
        $this->s->windowMaximize();
    }

    public function open($url)
    {
        $this->s->open($url);
    }

    public function getSelectedLabel($id)
    {
        return $this->s->getSelectedLabel($id);
    }
    
    public function getText($id)
    {
        return $this->s->getText($id);
    }
    
    public function tearDown()
    {
        $this->s->stop();
    }

    public function click($id)
    {
        $this->s->click($id);
    }
    
    public function getLocation()
    {
        return $this->s->getLocation();
    }

    public function select($id, $val)
    {
        $this->s->select($id, $val);
    }

    public function mustNotExist($id)
    {
        if ($this->isElementPresent($id))
        {
            throw new Exception("Element $id exists");
        }
    }   
    
    public function isVisible($id)
    {
        return $this->s->isVisible($id);
    }    
    
    public function isElementPresent($id)
    {
        return $this->s->isElementPresent($id);
    }

    public function mouseOver($id)
    {
        $this->s->mouseOver($id);
    }

    public function type($id, $val)
    {
        $this->s->type($id, $val);
    }

    public function check($id)
    {
        $this->s->check($id);
    }
    
    public function waitForPageToLoad($timeout = 10000)
    {
        $this->s->waitForPageToLoad($timeout);
    }

    public function submitForm($button = "//button")
    {
        $this->clickAndWait($button);
    }

    public function clickAndWait($selector)
    {
        $this->s->click($selector);
        $this->s->waitForPageToLoad(10000);
    }
    
    public function isElementInPagedList($elem)
    {
        while (true)
        {
            if ($this->isElementPresent($elem))
            {
                return true;
            }
        
            if (!$this->isElementPresent("//a[@class='pagination_next']"))
            {
                break;
            }
            $this->clickAndWait("//a[@class='pagination_next']");
        }        
        return false;
    }    
    
    public function typeInFrame($selector, $value)
    {
        retry(array($this->s, 'selectFrame'), array($selector));
        $this->s->type("//body", $value);
        $this->s->selectFrame("relative=top");
    }

    public function getLastEmail($match = null)
    {
        return retry(array($this, '_getLastEmail'), array($match));
    }

    public function _getLastEmail($match)
    {
        global $MOCK_MAIL_FILE;
        
        if (!file_exists($MOCK_MAIL_FILE))
        {    
            throw new Exception("no emails in file");
        }        
        $contents = file_get_contents($MOCK_MAIL_FILE);
        
        $matchPos = strrpos($contents, $match);
        if ($matchPos === false)
        {
            throw new Exception("'$match' not found in email");
        }
        
        $endPos = strpos($contents, '--------', $matchPos);
        if ($endPos === false)
        {
            throw new Exception("full email not yet written to file");
        }
        
        $startPos = strrpos($contents, "========", $matchPos - strlen($contents));
        if ($startPos === false)
        {
            throw new Exception("email start marker not found");
        }
        
        $email = substr($contents, $startPos, $endPos - $startPos);                    
                
        return $email;
    }

    public function getLinkFromEmail($email)
    {
        if (!preg_match('/http:[^\\s]+/', $email, $matches))
        {
            throw new Exception("couldn't find link in email $email");
        }
        return $matches[0];
    }

    public function setUrl($url)
    {
        // for some reason open() loads the action twice?
        $this->s->getEval("window.location.href='$url';");
        $this->s->waitForPageToLoad(10000);
    }
}


function retry($fn, $args, $timeout = 15)
{
    $time = time();
    while (true)
    {
        try
        {
            return call_user_func_array($fn, $args);
        }
        catch (Exception $ex)
        {
        }

        if (time() - $time > $timeout)
        {
            break;
        }

        sleep(0.25);
    }
    return call_user_func_array($fn, $args);
}