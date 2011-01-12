<?php

class RegisterTest extends SeleniumTest
{
    private $username;
    private $username2;

    public function test()
    {        
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
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->clickAndWait("//a[contains(@href,'pg/forgot_password')]");
        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();

        $email = $this->getLastEmail("Request for new password");

        $url = $this->getLinkFromEmail($email);
        echo "\n$url\n";

        $this->setUrl($url);

        $this->type("//input[@name='password']", "abcdefg");
        $this->type("//input[@name='password2']", "abcdefgh");
        $this->submitForm();
        
        $this->mouseOver("//div[@class='bad_messages']");
        
        $this->type("//input[@name='password']", "abcdefgh");
        $this->submitForm();

        $this->mouseOver("//div[@class='good_messages']");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        
        $this->setUrl($url); // password code can only be used once        
        $this->mouseOver("//div[@class='bad_messages']");
        $this->clickAndWait("//a[contains(@href, 'home')]");
    }

    private function _testRegister()
    {
        $this->open("/");

        $this->clickAndWait("//a[contains(@href,'org/new')]");

        /* qualification */
        $this->click("//input[@name='org_type' and @value='p']");
        $this->submitForm();
        $this->click("//input[@name='org_type' and @value='np']");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");
        $this->click("//input[@name='country' and @value='other']");
        $this->submitForm();
        sleep(2);
        $this->mouseOver("//div[@class='bad_messages']");
        $this->click("//input[@name='country' and @value='tz']");
        $this->submitForm();

        /* create account */

        $this->s->mouseOver("//div[@class='good_messages']");

        $this->type("//input[@name='org_name']", "testorg");
        $this->type("//input[@name='username']", "t<b>x</b>");
        $this->type("//input[@name='password']", "password");
        $this->type("//input[@name='password2']", "password2");
        $this->type("//input[@name='email']", "adunar@gmail.com");
        $this->submitForm();
        sleep(2);
        $this->mouseOver("//div[@class='bad_messages']");

        $this->username = "selenium".time();

        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
        sleep(2);
        $this->mouseOver("//div[@class='bad_messages']");

        $this->type("//input[@name='password2']", "password");
        $this->type("//input[@name='username']", "org");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");

        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
        sleep(2);

        /* set up homepage */
        $this->mouseOver("//div[@class='good_messages']");

        $this->typeInFrame("//iframe", "testing the website");
        $this->check("//input[@name='sector[]' and @value='3']");
        $this->check("//input[@name='sector[]' and @value='99']");
        $this->type("//input[@name='sector_other']", "another sector");
        $this->type("//input[@name='city']", "Wete");
        $this->select("//select[@name='region']", "Pemba North");
        $this->select("//select[@name='theme']", 'Bricks');

        $this->submitForm();

        /* home page */

        $this->mouseOver("//div[@class='good_messages']");

        $this->mouseOver("//h2[text()='testorg']");
        $this->mouseOver("//h3[text()='Wete, Tanzania']");
        $this->mouseOver("//a[contains(@href,'org/browse?list=1&sector=3') and text()='Conflict resolution']");
    }

    private function _testPost()
    {
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
        $this->typeInFrame("//iframe", "this is a test post");
        $this->submitForm();
        $this->mouseOver("//div[@class='blog_post']//p[contains(text(), 'this is a test post')]");
        
        $this->clickAndWait("//a[contains(@href,'org/feed')]");
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), 'this is a test post')]");
        $this->clickAndWait("//a[contains(text(), 'News update')]");
        $this->mouseOver("//div[@class='blog_post']//p[contains(text(), 'this is a test post')]");
        
    }

    private function _testEditContact()
    {
        $this->clickAndWait("//div[@id='site_menu']//a[contains(@href,'contact')]");
        $this->mouseOver("//a[@href='mailto:adunar@gmail.com']");
        $this->clickAndWait("//a[contains(@href,'contact/edit')]");
        $this->s->uncheck("//input[@name='public_email[]']");
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
        $this->mouseOver("//a[contains(@href,'contact')]"); // todo items
        $this->mustNotExist("//div[@id='site_menu']//a[contains(@href,'contact')]");
    }

    private function _testTranslate()
    {
        $this->clickAndWait("//a[contains(@href,'lang=sw')]");
        $this->mouseOver("//div[@class='section_header' and contains(text(),'Lengo')]");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'website')]");
        $this->clickAndWait("//a[contains(@href,'trans=3')]");
        $this->mustNotExist("//div[contains(@class, 'section_content')]//p[contains(text(),'website')]");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'tovuti')]");
        $this->clickAndWait("//a[contains(@href,'lang=en')]");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'website')]");
    }

    private function _testEditHome()
    {
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->typeInFrame("//iframe", "new mission!");
        $this->clickAndWait("//button[@name='submit']");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'new mission!')]");
        $this->mouseOver("//div[@id='site_menu']//a[@class='selected']");
    }

    private function _testEditPages()
    {
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->clickAndWait("//a[contains(@href,'projects/edit')]");
        $this->typeInFrame("//iframe", "we test stuff");
        $this->clickAndWait("//button[@name='submit']");
        sleep(2);
        $this->mouseOver("//div[contains(@class,'section_content')]//p[contains(text(), 'we test stuff')]");
    }

    private function _testSettings()
    {
        $this->clickAndWait("//a[contains(@href,'/settings')]");
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
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
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
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'org/feed')]");
        $this->mustNotExist("//a[contains(@href, '/{$this->username}')]");
        $this->clickAndWait("//a[contains(@href,'org/search')]");
        $this->type("//input[@name='q']", $this->username);
        $this->submitForm();
        $this->mouseOver("//div[@class='padded' and contains(text(),'No results')]");

        $this->open("/{$this->username}");
        sleep(2);
        $this->mouseOver("//div[@class='good_messages']");
        $this->mustNotExist("//h2");

        $this->clickAndWait("//a[contains(@href,'?login=1')]");
        $this->type("//input[@name='username']",'testadmin');
        $this->type("//input[@name='password']",'testtest');
        $this->submitForm();

        $this->clickAndWait("//a[contains(@href, 'approval=2')]");
        $this->s->getConfirmation();
        $this->mouseOver("//div[@class='good_messages']");

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");

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
        //$this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'home')]");
        $this->clickAndWait("//a[contains(@href,'org/new')]");

        /* qualification */
        $this->click("//input[@name='org_type' and @value='np']");
        $this->click("//input[@name='country' and @value='tz']");
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

        $this->typeInFrame("//iframe", "being a partner");
        $this->check("//input[@name='sector[]' and @value='4']");
        $this->check("//input[@name='sector[]' and @value='99']");
        $this->type("//input[@name='city']", "Konde");
        $this->select("//select[@name='region']", "Pemba North");
        $this->select("//select[@name='theme']","Woven Grass");

        $this->submitForm();

        /* home page */

        $this->mouseOver("//div[@class='good_messages']");

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");

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

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->type("//input[@name='username']","{$this->username2}");
        $this->type("//input[@name='password']",'password');
        $this->submitForm();

        $this->mouseOver("//div[@class='good_messages']");

        $this->open("/{$this->username}");
        $this->click("//a[contains(@href,'request_partner')]");
        $this->s->getConfirmation();

        $email = $this->getLastEmail("wants to add");

        $url = $this->getLinkFromEmail($email);

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");

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

    private function _testDeleteOrg()
    {
        $this->open("/admin/user");
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
            sleep(1);

            try
            {
                $this->clickAndWait("//a[contains(@href,'approval=-1')]");
                $this->s->getConfirmation();
            }
            catch (Testing_Selenium_Exception $ex) {}

            sleep(1);
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
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
    }
}
