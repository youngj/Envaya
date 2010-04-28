<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Selenium.php';
require_once 'PHPUnit/Framework.php';

class RegisterTest extends PHPUnit_Framework_TestCase
{
    private $s;
    private $username;
    private $username2;

    public function setUp()
    {
        $this->s = new Testing_Selenium("*firefox", "http://localhost");
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

    public function getLastEmail($match = null)
    {
        $time = time();
        
        while (true)
        {
            $contents = file_get_contents(dirname(__FILE__)."/mail.out");

            $marker = strrpos($contents, "========");
           
            $email = substr($contents, $marker);
            
            if ($email && strpos($email, '--------') && ($match == null || strpos($email, $match) !== false))
            {
                return $email;
            }
            
            if (time() - $time > 7)
                throw new Exception("couldn't find matching email");
        }    
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

    public function test()
    {        
        $this->_testContactForm();
        $this->_testRegister();
        $this->_testResetPassword();
        $this->_testSettings();        
        $this->_testTranslate();
        $this->_testPost();
        $this->_testEditPages();
        $this->_testEditContact();
        $this->_testEditHome();        
        $this->_testMakePublic();
        $this->_testPartnership();
        $this->_testMessages();
        $this->_testDeleteOrg();
    }
    
    private function _testResetPassword()
    {
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->clickAndWait("//a[contains(@href,'account/forgotten_password')]");
        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
        
        $email = $this->getLastEmail("Request for new password");
                
        $url = $this->getLinkFromEmail($email);

        $this->setUrl($url);
        
        $this->mouseOver("//div[@class='good_messages']");
        
        $email = $this->getLastEmail("Password reset");
        if (!preg_match('/reset to: (\\w+)/', $email, $matches))
        {
            throw new Exception("couldn't find password in email $email");
        }        
        $password = $matches[1];
        
        $this->type("//input[@name='username']", $this->username);
        $this->type("//input[@name='password']", $password);
        $this->submitForm();
        
        $this->mouseOver("//div[@class='good_messages']");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
    }
    
    private function _testRegister()
    {
        $this->open("/");
                       
        $this->clickAndWait("//a[contains(@href,'page/home')]");            

        $this->clickAndWait("//a[contains(@href,'org/new')]");        

        /* qualification */
        $this->click("//input[@name='org_type' and @value='p']");
        $this->submitForm();
        $this->click("//input[@name='org_type' and @value='np']");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");
        $this->click("//input[@name='country' and @value='other']");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");        
        $this->click("//input[@name='country' and @value='tz']");        
        $this->check("//input[@name='org_info[]' and @value='citizen']");
        $this->submitForm();        
        
        /* create account */
        
        $this->s->mouseOver("//div[@class='good_messages']");
        
        $this->type("//input[@name='org_name']", "testorg");
        $this->type("//input[@name='username']", "t<b>x</b>");
        $this->type("//input[@name='password']", "password");
        $this->type("//input[@name='password2']", "password2");        
        $this->type("//input[@name='email']", "adunar@gmail.com");
        $this->submitForm();        
        $this->mouseOver("//div[@class='bad_messages']");        

        $this->username = "selenium".time();

        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");        

        $this->type("//input[@name='password2']", "password");
        $this->type("//input[@name='username']", "org");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");        

        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();                
        
        /* set up homepage */

        $this->mouseOver("//div[@class='good_messages']");   
        
        $this->type("//textarea[@name='mission']", "testing the website");
        $this->check("//input[@name='sector[]' and @value='3']");
        $this->check("//input[@name='sector[]' and @value='99']");
        $this->type("//input[@name='sector_other']", "another sector");
        $this->type("//input[@name='city']", "Wete");        
        $this->select("//select[@name='region']", "Pemba North");                
        $this->click("//input[@name='theme' and @value='brick']");        
        
        $this->submitForm();

        /* home page */        
        
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->mouseOver("//h2[text()='testorg']");
        $this->mouseOver("//h3[text()='Wete, Tanzania']");
        $this->mouseOver("//a[contains(@href,'org/browse?list=1&sector=3') and text()='Conflict resolution']");           
    }
    
    private function _testPost()
    {
        $this->clickAndWait("//a[contains(@href,'pg/dashboard')]");
        $this->type("//textarea[@name='blogbody']", "this is a test post");
        $this->submitForm();
        $this->mouseOver("//div[@class='blog_post' and contains(text(), 'this is a test post')]");        
    }
    
    private function _testEditContact()
    {
        $this->clickAndWait("//a[contains(@href,'contact')]");
        $this->mouseOver("//a[@href='mailto:adunar@gmail.com']");
        $this->clickAndWait("//a[contains(@href,'contact/edit')]");
        $this->click("//input[@name='public_email' and @value='no']");
        $this->type("//input[@name='phone_number']", "1234567");
        $this->type("//input[@name='contact_name']", "Test Person");
        
        $this->clickAndWait("//button[@name='submit']");
        
        $this->mouseOver("//td[contains(text(),'1234567')]");
        $this->mouseOver("//td[contains(text(),'Test Person')]");        
        $this->mustNotExist("//a[@href='mailto:adunar@gmail.com']");
        
        $this->clickAndWait("//a[contains(@href,'contact/edit')]");
        $this->clickAndWait("//button[@id='widget_delete']");
        $this->s->getConfirmation();
        $this->mouseOver("//div[@class='good_messages']");
        $this->mustNotExist("//a[contains(@href,'contact')]");
    }
    
    private function _testTranslate()
    {
        $this->clickAndWait("//a[contains(@href,'action/changeLanguage?newLang=sw')]");
        $this->mouseOver("//div[@class='section_header' and contains(text(),'Lengo')]");
        $this->mouseOver("//div[contains(@class, 'section_content') and contains(text(),'website')]");
        $this->clickAndWait("//a[contains(@href,'trans=3')]");
        $this->mustNotExist("//div[contains(@class, 'section_content') and contains(text(),'website')]");
        $this->mouseOver("//div[contains(@class, 'section_content') and contains(text(),'tovuti')]");
        $this->clickAndWait("//a[contains(@href,'action/changeLanguage?newLang=en')]");
        $this->mouseOver("//div[contains(@class, 'section_content') and contains(text(),'website')]");
    }
    
    private function _testEditHome()
    {
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->type("//textarea[@name='content']", "new mission!");
        $this->clickAndWait("//button[@name='submit']");
        $this->mouseOver("//div[contains(@class, 'section_content') and contains(text(),'new mission!')]");
        $this->mouseOver("//div[@id='site_menu']//a[@class='selected']");
    }
    
    
    private function _testEditPages()
    {
        $this->clickAndWait("//a[contains(@href,'pg/dashboard')]");           
        $this->clickAndWait("//a[contains(@href,'home/edit')]");           
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->clickAndWait("//a[contains(@href,'projects/edit')]");           
        $this->type("//textarea[@name='content']", "we test stuff");
        $this->clickAndWait("//button[@name='submit']");
        $this->mouseOver("//div[contains(@class,'section_content') and contains(text(), 'we test stuff')]");
        $this->clickAndWait("//a[contains(@href,'pg/dashboard')]");    
        $this->clickAndWait("//a[contains(@href,'team/edit')]");    
        
        $this->type("//input[@name='name']", "Person 1");
        $this->type("//textarea[@name='description']", "Description 1");
        $this->clickAndWait("//button[@name='submit']");
        
        $this->type("//input[@name='name']", "Person 2");
        $this->type("//textarea[@name='description']", "Description 2");
        $this->clickAndWait("//button[@name='submit']");
        
        $this->type("//input[@name='name']", "Person 3");
        $this->type("//textarea[@name='description']", "Description 3");
        $this->clickAndWait("//button[@name='submit']");             
        
        $this->clickAndWait("//a[contains(@href,'teammember/')]");
        $this->mouseOver("//input[@name='name' and @value='Person 1']");
        $this->type("//input[@name='name']", "Person 0");
        $this->type("//textarea[@name='description']", "Description 0");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        
        $this->mouseOver("//div[@class='team_member_name' and contains(text(),'Person 0')]");        
        $this->mouseOver("//div[@class='team_member_name' and contains(text(),'Person 2')]");        
        $this->mustNotExist("//div[@class='team_member_name' and contains(text(),'Person 1')]");        
        
        $this->clickAndWait("//a[contains(@href,'team/edit')]");    
        $this->clickAndWait("//a[contains(@href,'moveTeamMember')]");    
        $this->clickAndWait("//a[contains(@href,'deleteTeamMember')]");    
        $this->s->getConfirmation();
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        
        $this->mouseOver("//div[@class='team_member_name' and contains(text(),'Person 0')]");        
        $this->mouseOver("//div[@class='team_member_name' and contains(text(),'Person 3')]");        
        $this->mustNotExist("//div[@class='team_member_name' and contains(text(),'Person 2')]");        
    }        
    
    private function _testSettings()
    {
        $this->clickAndWait("//a[contains(@href,'pg/settings')]");
        $this->type("//input[@name='name']", "New Name");
        $this->type("//input[@name='password']", "password2");
        $this->type("//input[@name='password2']", "password3");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");
        $this->type("//input[@name='password']", "password2");
        $this->type("//input[@name='password2']", "password2");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        $this->mouseOver("//h2[text()='New Name']");
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->type("//input[@name='username']",$this->username);        
        $this->type("//input[@name='password']",'password');
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");
        $this->type("//input[@name='username']",$this->username);        
        $this->type("//input[@name='password']",'password2');
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");        
    }
        
    private function _testMakePublic()
    {
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
        $this->clickAndWait("//a[contains(@href,'org/feed')]");
        $this->mustNotExist("//a[contains(@href, '/{$this->username}')]");
        $this->clickAndWait("//a[contains(@href,'org/search')]");
        $this->type("//input[@name='q']", $this->username);
        $this->submitForm();
        $this->mouseOver("//div[@class='padded' and contains(text(),'No results')]");
        
        $this->open("/{$this->username}");
        $this->mouseOver("//div[@class='good_messages']");
        $this->mustNotExist("//h2");
        
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->type("//input[@name='username']",'testadmin');        
        $this->type("//input[@name='password']",'testtest');
        $this->submitForm();
        
        $this->clickAndWait("//a[contains(@href,'pg/admin')]");
        $this->clickAndWait("//a[contains(@href,'pg/admin/user')]");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        $this->clickAndWait("//a[contains(@href, 'approval=2')]");
        $this->s->getConfirmation();
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->clickAndWait("//a[contains(@href,'action/logout')]");

        $this->clickAndWait("//a[contains(@href,'org/search')]");
        $this->type("//input[@name='q']", $this->username);
        $this->submitForm();
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        
        $this->mouseOver("//h2[text()='New Name']");
        $this->mustNotExist("//a[contains(@href,'home/edit')]");
        
        $this->clickAndWait("//a[contains(@href,'org/feed')]");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");        
    }
    
    private function _testPartnership()
    {
        //$this->clickAndWait("//a[contains(@href,'action/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/home')]");        
        $this->clickAndWait("//a[contains(@href,'org/new')]");        

        /* qualification */
        $this->click("//input[@name='org_type' and @value='np']");
        $this->click("//input[@name='country' and @value='tz']");        
        $this->check("//input[@name='org_info[]' and @value='citizen']");
        $this->submitForm();        
        
        /* create account */
        
        $this->s->mouseOver("//div[@class='good_messages']");
        
        $this->username2 = "selenium".time();
        
        $this->type("//input[@name='org_name']", "Test Org Partner");
        $this->type("//input[@name='username']", $this->username2);
        $this->type("//input[@name='password']", "password");
        $this->type("//input[@name='password2']", "password");        
        $this->type("//input[@name='email']", "adunar+foo@gmail.com");
        $this->submitForm();        
        
        /* set up homepage */

        $this->mouseOver("//div[@class='good_messages']");   
        
        $this->type("//textarea[@name='mission']", "being a partner");
        $this->check("//input[@name='sector[]' and @value='4']");
        $this->check("//input[@name='sector[]' and @value='99']");       
        $this->type("//input[@name='city']", "Konde");        
        $this->select("//select[@name='region']", "Pemba North");                
        $this->click("//input[@name='theme' and @value='wovengrass']");        
        
        $this->submitForm();

        /* home page */        
        
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
        
        $email = $this->getLastEmail("New organization registered");
        $url = $this->getLinkFromEmail($email);
        $this->setUrl($url);
        
        $this->type("//input[@name='username']",'testadmin');        
        $this->type("//input[@name='password']",'testtest');
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $this->open("/{$this->username2}");
        $this->clickAndWait("//a[contains(@href, 'approval=2')]");
        $this->s->getConfirmation();
        $this->mouseOver("//div[@class='good_messages']");        
        
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->type("//input[@name='username']","{$this->username2}");        
        $this->type("//input[@name='password']",'password');
        $this->submitForm();
        
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->open("/{$this->username}");
        $this->click("//a[contains(@href,'requestPartner')]");
        $this->s->getConfirmation();
        
        $email = $this->getLastEmail("wants to add");
        
        $url = $this->getLinkFromEmail($email);
        
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
        
        $this->setUrl($url);
        
        $this->type("//input[@name='username']","{$this->username}");        
        $this->type("//input[@name='password']",'password2');
        $this->submitForm();
        
        $this->mouseOver("//div[@class='good_messages']");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $this->mouseOver("//a[@class='feed_org_name' and contains(@href,'/{$this->username2}')]");
        $this->clickAndWait("//a[contains(@href,'partnerships/edit')]");
        $this->type("//textarea", 'We work together on stuff');
        $this->clickAndWait("//button[@name='submit']");
        $this->mouseOver("//span[contains(text(),'We work together on stuff')]");
        $this->clickAndWait("//a[@class='feed_org_name' and contains(@href,'/{$this->username2}')]");
        $this->clickAndWait("//a[contains(@href,'/partnerships')]");
        $this->mouseOver("//a[@class='feed_org_name' and contains(@href,'/{$this->username}')]");
    }
    
    private function _testMessages()
    {
        $this->open("/{$this->username2}");
        $this->clickAndWait("//a[contains(@href,'/compose')]");
        $this->type("//input[@name='subject']","Test Subject");
        $this->type("//textarea[@name='message']", "Test Message");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $email = $this->getLastEmail("Test Subject");
        
        $this->assertContains("Test Message",$email);
        $this->assertContains('To: "Test Org Partner" <adunar+foo@gmail.com>', $email);
        $this->assertContains('Reply-To: "New Name" <adunar@gmail.com>', $email);
    }
    
    private function _testContactForm()
    {
        $this->open("/page/contact");
        $this->type("//textarea[@name='message']", "contact message");
        $this->type("//input[@name='name']", "contact name");
        $this->type("//input[@name='email']", "adunar+bar@gmail.com");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        
        $email = $this->getLastEmail("User feedback");

        $this->assertContains("contact message",$email);
        $this->assertContains('contact name', $email);
        $this->assertContains('adunar+bar@gmail.com', $email);        
    }
    
    private function _testDeleteOrg()
    {
        $this->open("/pg/admin/user/");
        $this->type("//input[@name='username']",'testadmin');        
        $this->type("//input[@name='password']",'testtest');
        $this->submitForm();
        
        $this->clickAndWait("//a[contains(@href,'selenium')]");
        
        while (true)
        {              
            try
            {
                $this->clickAndWait("//a[contains(@href,'approval=0')]");
                $this->s->getConfirmation();
            }
            catch (Testing_Selenium_Exception $ex) {}
            
            try
            {
                $this->clickAndWait("//a[contains(@href,'approval=-1')]");
                $this->s->getConfirmation();
            }
            catch (Testing_Selenium_Exception $ex) {}
            
            $this->clickAndWait("//a[contains(@href,'delete')]");
            $this->s->getConfirmation();
            
            $this->mouseOver("//div[@class='good_messages']");
            try
            {
                $this->clickAndWait("//a[contains(@href,'selenium')]");
            }
            catch (Testing_Selenium_Exception $ex)
            {
                break;
            }
        }            
        $this->clickAndWait("//a[contains(@href,'action/logout')]");
    }
}

