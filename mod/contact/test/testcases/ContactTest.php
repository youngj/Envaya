<?php

class ContactTest extends SeleniumTest
{
    function test()
    {
        $this->open('/admin/contact');
        $this->login('testadmin','secretpassw0rd');
        $this->assertEquals("testadmin", $this->getText("//span[@id='top_whoami']"));
           
        // reset batch email notification settings
        $this->open("/testposter0");        
        $this->clickAndWait("//a[contains(@href,'pg/email_settings')]");        
        $this->uncheck("//label[contains(text(),'announcements')]//input");
        $this->submitForm();
        
        $this->open("/testposter11");    
        $this->clickAndWait("//a[contains(@href,'pg/email_settings')]");        
        $this->check("//label[contains(text(),'announcements')]//input");        
        $this->submitForm();

        $this->open("/testposter10");    
        $this->clickAndWait("//a[contains(@href,'pg/email_settings')]");        
        $this->check("//label[contains(text(),'announcements')]//input");                
        $this->submitForm();
        
        // delete selenium email templates
        $this->open('/admin/contact/email');
        $this->deleteEmailTemplates();
           
        // add new email template, with placeholders
        $this->clickAndWait("//a[contains(@href,'email/add')]");
        $this->type("//input[@name='from']", "ContactTest");
        $this->type("//input[@name='subject']", "Selenium Test for {{username}}");
        $this->setTinymceContent("<p>Hi {{name}},</p><p>This is a test of the emergency broadcast system.</p>");
        $this->submitForm();
        
        $this->mouseOver("//span[contains(text(), 'ContactTest')]");
        $this->mouseOver("//span[contains(text(), 'Selenium Test for {{username}}')]");
        
        $this->selectFrame("//iframe");
        $this->mouseOver("//p[contains(text(), 'emergency broadcast system')]");
        $this->selectFrame('relative=top');
        
        // edit email template              
        $this->clickAndWait("//div[@id='top_menu']//a[contains(@href,'/edit')]");
        
        $this->assertEquals('ContactTest', $this->getValue("//input[@name='from']"));
        $this->setTinymceContent("<p>Hi {{name}},</p><p>This is not a test.</p>");
        $this->submitForm();
        
        
        // send email template to all users        
        $this->clickAndWait("//a[contains(@href, '/send')]");
        $this->mouseOver("//label[contains(text(),'Test Poster11')]");
        $this->mouseOver("//label[contains(text(),'Test Poster10')]");
        $this->mustNotExist("//label[contains(text(),'Test Poster0')]");
        $this->submitForm();
        $this->ensureGoodMessage();
        
        // verify correct users received email, with correct placeholders
        $email = $this->getLastEmail("Test Poster11");
        $this->assertContains("+p11", $email);
        $this->assertContains("To: Test Poster11", $email);
        $this->assertContains("From: ContactTest", $email);
        $this->assertContains("Subject: Selenium Test for testposter11", $email);
        $this->assertContains("Hi Test Poster11,", $email);
        $this->assertContains("This is not a test", $email);
        $this->assertNotContains("Test Poster10", $email);        
        
        $email = $this->getLastEmail("Test Poster10");
        $this->assertContains("+p10", $email);
        $this->assertContains("To: Test Poster10", $email);
        $this->assertContains("Subject: Selenium Test for testposter10", $email);
        $this->assertContains("Hi Test Poster10,", $email);
        $this->assertNotContains("Test Poster11", $email);        
        
        $this->assertNoEmail("Test Poster0");
        $this->assertNoEmail("+p0");

        $end_headers = strpos($email, "\r\n\r\n");        
        $email_body = substr($email, $end_headers + 4);
        
        $dom = new DOMDocument;
        @$dom->loadHTML($email_body);                
        $links = $dom->getElementsByTagName('a');

        $link = $links->item(0);
        $url = $link->getAttribute('href');
        $url = str_replace('+', '%2B', $url); // dom document seems to unescape urls?
        
        // unsubscribe testposter10
        $this->logout();
        
        $this->open($url);
        $this->retry('uncheck', array("//label[contains(text(),'announcements')]//input"));
        $this->submitForm();
        $this->ensureGoodMessage();       
        
        // add another email template
        $this->open("/admin/contact/email");
        $this->login("testadmin","secretpassw0rd");
        $this->assertEquals("testadmin", $this->getText("//span[@id='top_whoami']"));
        
        $this->clickAndWait("//a[contains(@href,'email/add')]");
        $this->type("//input[@name='from']", "ContactTest");
        $this->type("//input[@name='subject']", "Another Selenium email");
        $this->setTinymceContent("<p>hello</p>");
        $this->submitForm();
        
        $this->clickAndWait("//a[contains(@href, '/send')]");
        $this->mouseOver("//label[contains(text(),'Test Poster11')]");
        $this->mustNotExist("//label[contains(text(),'Test Poster10')]");
        $this->mustNotExist("//label[contains(text(),'Test Poster0')]");        
                
        // test contact list filters        
        $this->open("/admin/contact");
        $this->mouseOver("//a[contains(@href,'testposter10/settings')]");        

        $this->select("//select[@name='sector']", "Health");
        $this->waitForPageToLoad();
        $this->mustNotExist("//a[contains(@href,'testposter10/settings')]");        
        
        $this->select("//select[@name='sector']", "Education");
        $this->waitForPageToLoad();
        $this->mouseOver("//a[contains(@href,'testposter10/settings')]");        
        
        $this->clickAndWait("//a[contains(@href,'offset=15')]");
        $this->assertEquals("6", $this->getValue("//select[@name='sector']"));
        
        // test inviting email via contact list
        $this->clickAndWait("//a[contains(@href,'email/subscription')]");
        $address = $this->getText("//a[contains(@href,'mailto:')]");
        
        $this->clickAndWait("//a[contains(@href,'/send')]");
        $this->assertEquals("1", $this->getText("//span[@id='recipient_count']"));
        $this->submitForm();
        $this->ensureGoodMessage();
        
        $email = $this->getLastEmail("Subject: Another Selenium email");
        $this->assertContains("hello", $email);
        $this->assertContains($address, $email);

        // test 'reset' link to allow sending email twice        
        $this->clickAndWait("//a[contains(@href,'email/subscription')]");
        $this->assertEquals($address, $this->getText("//a[contains(@href,'mailto:')]"));
        $this->mustNotExist("//a[contains(@href,'/send')]");
        $this->clickAndWait("//div[@id='main_content']//a[contains(@href,'postLink')]");
        $this->clickAndWait("//a[contains(@href,'/send')]");
        $this->assertEquals("1", $this->getText("//span[@id='recipient_count']"));
        $this->submitForm();
        $this->ensureGoodMessage();        
    }
    
    function deleteEmailTemplates()
    {
        while (true)
        {
            try
            {
                $this->clickAndWait("//div[@class='email_item' and .//strong[contains(text(), 'Selenium')]]//a[contains(@href,'/edit')]");
            }
            catch (Exception $ex)
            {
                return;
            }
            
            $this->click("//button[@id='widget_delete']");
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
            $this->mouseOver("//a[contains(@href,'email/add')]");
        }
    }
}