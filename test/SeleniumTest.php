<?php

/*
 * Base class for Envaya's Selenium tests in /test/testcases .
 */

require_once 'Selenium.php';

class SeleniumTest extends PHPUnit_Framework_TestCase
{
    protected $s;
    
    public function setUp()
    {
        global $TEST_CONFIG, $BROWSER;
        $this->root = dirname(__DIR__);
        $this->config = $TEST_CONFIG;
        $this->browser = $BROWSER;
        $this->startBrowser();
        $this->setTimestamp(time());
    }
    
    public function startBrowser()
    {
        $this->s = $this->init_selenium();
        $this->start();
        $this->windowMaximize();
    }      
        
    public function init_selenium()
    {
        return new Testing_Selenium("*{$this->browser}", "http://{$this->config['domain']}");    
    }

    /*
     * Allows all functions on selenium object $this->s to be called directly on SeleniumTest object,
     * e.g. $this->click('//a');
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->s, $name), $arguments);
    }
    
    public function tearDown()
    {
        $this->s->stop();
    }
    
    public function setTimestamp($timestamp)
    {
        file_put_contents($this->config['time:mock_file'], "$timestamp");
    }

    function login($username, $password)
    {
        $this->waitForElement("//input[@name='username']");
        $this->type("//input[@name='username']",$username);
        $this->type("//input[@name='password']",$password);
        $this->submitForm();    
    }

    function logout()
    {
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");        
        $this->submitForm();
        $this->waitForElement("//a[@id='loginButton']");    
    }    
    
    function waitForElement($xpath, $timeout = 15)
    {
        $this->retry('mouseOver', array($xpath), $timeout);
    }    
    
    function runScript($cmd)
    {
        $root = dirname(__DIR__);
        $env = get_environment();    
        $env["ENVAYA_CONFIG"] = json_encode($this->config);
        run_task_sync($cmd, $root, $env);
    }
    
    function getReplyTo($email)
    {        
        if (!preg_match('#Reply-To:\s+(?P<reply_to>[\.\@\+\-\w]+)#', $email, $match))
        {
            throw new Exception('email has no Reply-To header');
        }    
        return $match['reply_to'];
    }
    
    function receiveMail($args)
    {
        $subject = '';
        $body = '';
        $to = '';
        $from = '';
        extract($args);       
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "http://{$this->config['domain']}/pg/receive_mail?secret={$this->config['mail:sendgrid_secret']}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);    
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'subject' => $subject,
            'to' => $to,
            'from' => $from,
            'text' => $body
        ));

        $json = curl_exec($ch);        
    }
    
    function ensureGoodMessage()
    {
        $this->mouseOver("//div[@class='good_messages']");
    }
    
    function ensureBadMessage()
    {
        $this->mouseOver("//div[@class='bad_messages']");
    }
    
    public function mustNotExist($id)
    {
        if ($this->isElementPresent($id))
        {
            throw new Exception("Element $id exists");
        }
    }   
    
    public function mustBeVisible($id)
    {
        if (!$this->isVisible($id))
        {
            throw new Exception("Element $id is not visible");
        }
    }       
    
    public function mustNotBeVisible($id)
    {
        if ($this->isVisible($id))
        {
            throw new Exception("Element $id is visible");
        }
    }           
        
    public function waitForPageToLoad($timeout = 10000)
    {
        $this->s->waitForPageToLoad($timeout);
    }

    public function submitForm($button = "//button[@type='submit']")
    {
        $this->clickAndWait($button);
    }

    public function clickAndWait($selector)
    {
        $this->click($selector);
        $this->waitForPageToLoad(10000);
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
    
    public function setTinymceContent($text)
    {
        $this->retry('getEval', array('window.tinymce.activeEditor.setContent('.json_encode($text).')'));        
    }       

    public function getLastSMS($match = "Message")
    {
        return $this->retry('_getLastSMS', array($match));   
    }
    
    public function _getLastSMS($match = "Message")
    {
        $mock_sms_file = $this->config['sms:mock_file'];
        
        if (!file_exists($mock_sms_file))
        {    
            throw new Exception("no messages in file");
        }        
        $contents = file_get_contents($mock_sms_file);

        return $this->_matchMessage($match, $contents);
    }    
        
    
    public function getLastEmail($match = "Subject")
    {
        return $this->retry('_getLastEmail', array($match));
    }
    
    public function assertNoEmail($match = "Subject")
    {
        try
        {
            $this->_getLastEmail($match);
        }
        catch (Exception $ex) 
        {
            return;
        }
    
        throw new Exception("Found matching email for $match");
    }
    
    public function assertNoSMS($match = "Message")
    {
        try
        {
            $this->_getLastSMS($match);
        }
        catch (Exception $ex) 
        {
            return;
        }
    
        throw new Exception("Found matching sms for $match");
    }    

    public function _getLastEmail($match = "Subject")
    {
        $mock_mail_file = $this->config['mail:mock_file'];
        
        if (!file_exists($mock_mail_file))
        {    
            throw new Exception("no emails in file");
        }        
        $contents = file_get_contents($mock_mail_file);
        
        // decode quoted-printable
        $contents = str_replace("=\r\n","", $contents);
        $contents = preg_replace('/\=([A-F0-9][A-F0-9])/','%$1',$contents);
        $contents = rawurldecode($contents);                
        
        return $this->_matchMessage($match, $contents);
    }    
    
    public function _matchMessage($match, $contents)
    {       
        $matchPos = strrpos($contents, $match);
        if ($matchPos === false)
        {
            throw new Exception("'$match' not found in message");
        }
        
        $endPos = strpos($contents, '--------', $matchPos);
        if ($endPos === false)
        {
            throw new Exception("full message not yet written to file");
        }        
        
        $startPos = strrpos($contents, "========", $matchPos - strlen($contents));
        if ($startPos === false)
        {
            throw new Exception("message start marker not found");
        }                
        $startPos = strpos($contents, "\n", $startPos) + 1;
        
        $message = substr($contents, $startPos, $endPos - $startPos);                            
                                                        
        return $message;    
    }
    
    public function getLinkFromText($text, $index = 0)
    {
        if (!preg_match_all('/http:[^\\s>]+/', $text, $matches))
        {
            throw new Exception("couldn't find any links in $text");
        }
        if ($index >= sizeof($matches[0]))
        {
            throw new Exception("couldn't find link $index in $text");
        }
        return $matches[0][$index];
    }
    
    public function retry($fn_name, $args = null, $timeout = 15)
    {
        return retry(array($this, $fn_name), $args, $timeout);
    }
    
    public function setUrl($url)
    {
        // for some reason open() loads the action twice?
        $this->s->getEval("window.location.href='$url';");
        $this->s->waitForPageToLoad(10000);
    }    
    
    public function checkImage($imgUrl, $minBytes, $maxBytes)
    {
        $imgData = file_get_contents($imgUrl);
        $imgSize = strlen($imgData);
        $this->assertGreaterThan($minBytes, $imgSize);
        $this->assertLessThan($maxBytes, $imgSize);        
        
        $sizeArray = getimagesize($imgUrl);
        
        $this->assertTrue(is_array($sizeArray));
                
        $width = $sizeArray[0];
        $height = $sizeArray[1];
        
        $this->assertGreaterThan(10, $width);
        $this->assertGreaterThan(10, $height);
    }
     
    public function sendSMS($from, $to, $msg)
    {
        $url = "http://{$this->config['domain']}/sg/incoming?provider=Mock&from=".urlencode($from)
            ."&to=".urlencode($to)
            ."&msg=".urlencode($msg);
            
        $res = file_get_contents($url);
        
        $dom = new DOMDocument();
        $dom->loadXML($res);                            
        
        $replies = array();
        $smses = $dom->getElementsByTagName('Sms');
        for ($i = 0; $i < $smses->length; $i++)
        {
            $replies[] = $smses->item($i)->textContent;
        }        
        return $replies;
    }
     
    public function submitFakeCaptcha()
    {
        $answer = $this->getText("//b[@id='captcha_answer']");
        $this->type("//input[@name='captcha_response']", $answer);
        $this->submitForm();
    }
     
    function selectShareWindow()
    {
        $this->selectWindow('eshare');
        $this->mouseOver("//textarea");
    }
     
}


function retry($fn, $args = null, $timeout = 15)
{
    $time = time();
    if (!$args) 
    { 
        $args = array(); 
    }
    
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

        usleep(250000);
    }
    return call_user_func_array($fn, $args);
}