<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Selenium.php';
require_once 'PHPUnit/Framework.php';

class SeleniumTest extends PHPUnit_Framework_TestCase
{
    protected $s;
    
    public function setUp()
    {
        global $BROWSER;

        $this->s = new Testing_Selenium($BROWSER, "http://localhost");
        $this->s->start();
        $this->s->windowMaximize();
    }

    public function open($url)
    {
        $this->s->open($url);
    }

    public function tearDown()
    {
        $this->s->stop();
    }

    public function click($id)
    {
        $this->s->click($id);
    }

    public function select($id, $val)
    {
        $this->s->select($id, $val);
    }

    public function mustNotExist($id)
    {
        try
        {
            $this->mouseOver($id);
            throw new Exception("Element $id exists");
        }
        catch (Testing_Selenium_Exception $ex)
        {
        }
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

    public function submitForm()
    {
        $this->clickAndWait("//button");
    }

    public function clickAndWait($selector)
    {
        $this->s->click($selector);
        $this->s->waitForPageToLoad(10000);
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
        $mailFile = __DIR__."/mail.out";
        if (file_exists($mailFile))
        {
            $contents = file_get_contents($mailFile);

            $marker = strrpos($contents, "========");

            $email = substr($contents, $marker);

            if ($email && strpos($email, '--------') && ($match == null || strpos($email, $match) !== false))
            {
                return $email;
            }
        }
        throw new Exception("couldn't find matching email");
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