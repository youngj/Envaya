<?php

class CommentTest extends SeleniumTest
{
    public function test()
    {
        // assumes that recaptcha is disabled in settings file        
        $this->open('/pg/login');

        $this->login('testorg','testtest');
        $this->ensureGoodMessage();
        
        $content = "test post ".time();
       
        $this->typeInFrame("//iframe", $content);
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//div[@class='blog_post']//p[contains(text(), '$content')]");

        // comment as logged in user
        $this->type("//textarea[@name='content']", "comment number one");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->assertContains("comment number one", $this->getText("//div[@class='comment']"));
        $this->assertContains("Test Org", $this->getText("//div[@class='comment_name']"));
        $this->type("//textarea[@name='content']", "comment number one"); // duplicate comment
        $this->submitForm();
        $this->ensureBadMessage();
        
        $this->mouseOver("//div[@class='comment']//span[@class='admin_links']//a");  
        
        $url = $this->getLocation();
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        
        // comment as anonymous user        
        
        $this->open($url);        
        
        $this->type("//textarea[@name='content']", "comment number two");
        $this->type("//input[@name='name']", "random dude");
        $this->type("//input[@name='location']", "DSM");
        $this->submitForm();
        
        // test fake recaptcha 
        // (because we can't write an automated test for 'real' recaptcha without breaking it)
        
        $this->type("//input[@name='recaptcha_response_field']", "wrong");
        $this->submitForm();
        $this->ensureBadMessage();
        
        $this->submitFakeCaptcha();
        $this->ensureGoodMessage();
        
        $this->assertContains("comment number two", $this->getText("//div[@class='comment'][2]"));                
        $this->assertContains("random dude", $this->getText("//div[@class='comment'][2]//div[@class='comment_name']"));
                
        $this->assertContains("comment number one", $this->getText("//div[@class='comment']"));                        
        // delete your own comment
        $this->clickAndWait("//span[@class='admin_links']//a");
        
        $this->getConfirmation();
        $this->ensureGoodMessage();
        
        $this->assertContains("comment number one", $this->getText("//div[@class='comment']"));                        
        $this->assertContains("comment deleted", $this->getText("//div[@class='comment'][2]"));                        
    }
}