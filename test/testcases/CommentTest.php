<?php

class CommentTest extends WebDriverTest
{
    public function test()
    {
        // assumes that captcha is disabled in settings file        
        
        $this->open('/pg/login');

        $this->login('testorg','testtest');
        $this->retry('ensureGoodMessage', array('Welcome, Test Org'));
        
        $content = "test post ".time();
       
        $this->typeInFrame("//iframe", $content);
        $this->submitForm();
        $this->retry('ensureGoodMessage', array('saved successfully'));
        $this->mustExist("//div[@class='section_content padded']//p[contains(text(), '$content')]");

        // comment as logged in user
        $this->type("//textarea[@name='content']", "comment number one");
        $this->submitForm();
        $this->retry('ensureGoodMessage', array('comment has been published'));
        $this->assertContains("comment number one", $this->getText("//div[@class='comment']"));
        $this->assertContains("Test Org", $this->getText("//div[@class='comment_name']"));
        $this->type("//textarea[@name='content']", "comment number one"); // duplicate comment
        $this->submitForm();
        $this->retry('ensureBadMessage', array('same as an existing comment'));
        
        $this->mustExist("//div[@class='comment']//span[@class='admin_links']//a");  
        
        $url = $this->getLocation();
        
        $this->logout();
        
        // comment as anonymous user        
        
        $this->open($url);        
        
        $this->waitForElement("//textarea[@name='content']");
        $this->type("//textarea[@name='content']", "comment number two");
        $this->type("//input[@name='name']", "random dude");
        $this->type("//input[@name='location']", "DSM");
        $this->submitForm();
        
        // test fake captcha 
        // (because we can't write an automated test for 'real' captcha without breaking it)
        
        $this->retry('type', array("//input[@name='captcha_response']", "wrong"));
        $this->submitForm();
        $this->retry('ensureBadMessage', array("verification code"));
        
        $this->submitFakeCaptcha();
        $this->retry('ensureGoodMessage', array('comment has been published'));
        
        $this->assertContains("comment number two", $this->getText("//div[@class='comment'][2]"));                
        $this->assertContains("random dude", $this->getText("//div[@class='comment'][2]//div[@class='comment_name']"));                
        $this->assertContains("comment number one", $this->getText("//div[@class='comment']"));                        
        // delete your own comment
        $this->clickAndWait("//span[@class='admin_links']//a");
        
        $this->acceptAlert();
        $this->retry('ensureGoodMessage', array('Comment deleted'));        
        
        $this->assertContains("comment number one", $this->getText("//div[@class='comment']"));                        
        $this->assertContains("comment deleted", $this->getText("//div[@class='comment'][2]"));
        
        // test email notifications
        $email = $this->getLastEmail("random dude added a new comment");
        $this->assertContains($url, $email);
        $this->assertContains('comment number two', $email);
        
        $replyTo = $this->getReplyTo($email);
        
        // test replying to email notifications
        $this->receiveMail(array(
            'to' => $replyTo,
            'from' => 'Mr. Person <foo@nowhere.com>',
            'subject' => "Re: random dude added a new comment",
            'body' => "comment number three"
        ));
        
        $this->open($url);
        $this->assertContains("comment number three", $this->getText("//div[@class='comment'][3]"));                
        $this->assertContains("Mr. Person", $this->getText("//div[@class='comment'][3]//div[@class='comment_name']"));
    }
}