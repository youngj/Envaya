<?php

class DiscussionTest extends SeleniumTest
{
    public function test()
    {
        // delete existing existing discussions, discussion page
        $this->open('/pg/login');
        $this->login('testorg','testtest');
        $this->clickAndWait("//a[contains(@href,'page/discussions/edit')]");
        $this->deleteDiscussions();
        
        // test can't add discussion if not logged in and page doesn't exist
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->open("/testorg/discussions");
        $this->retry('mouseOver', array("//div[contains(text(),'not found')]"));
        $this->open("/testorg/topic/new");
        $this->retry('mouseOver', array("//div[contains(text(),'not found')]"));
        
        // test add new discussion as logged in user
        $this->open('/pg/login');
        $this->login('testorg','testtest');
        $this->clickAndWait("//a[contains(@href,'page/discussions/edit')]");
        $this->clickAndWait("//a[contains(@href,'topic/new')]");
        
        $this->type("//input[@name='subject']","My Zeroth Discussion");
        $this->typeInFrame("//iframe", "message 0");
        $this->type("//input[@name='name']", "....");
        $this->submitForm();
        $this->ensureGoodMessage();
        
        // test inviting other envaya users from list of organizations
        $this->click("//div[@class='good_messages']//p//a");        
        
        $this->retry('selectShareWindow');
        $this->retry('click', array("//a[@id='add_users']"));
        
        $this->retry('selectFrame', array("//iframe"));
        $this->retry('select', array("//select[@name='sector']", "Education"));
        $this->retry('click', array("//a[contains(@title,'+p0')]"));
        $this->retry('click', array("//a[contains(@title,'+p10')]"));
        $this->retry('click', array("//a[contains(@title,'+p11')]"));
        $this->retry('click', array("//a[contains(@title,'+p10')]"));
        $this->selectFrame("relative=top");
        $this->click("//input[@value='Close']");
        
        $emails = $this->getValue("//textarea[@name='emails']");
        $this->assertContains("+p0", $emails);
        $this->assertContains("+p11", $emails);
        $this->assertNotContains("+p10", $emails);
                
        $this->type("//textarea[@name='message']", "hiiiiii");
                
        $this->submitForm();
        $this->ensureGoodMessage();
        
        $email = $this->getLastEmail("hiiiiii");
        $this->assertContains('+p0', $email);
        $this->assertContains('+p11', $email);
        $this->assertNotContains('+p10', $email);
        
        $this->close();
        $this->selectWindow(null);                        
        
        $this->mouseOver("//h3[contains(text(),'My Zeroth Discussion')]");
        $this->mouseOver("//p[contains(text(),'message 0')]");
        $this->mouseOver("//a[contains(text(),'....')]");
        $this->clickAndWait("//a[contains(@href,'testorg/discussions')]");
        $this->mouseOver("//span[@class='feed_snippet' and contains(text(),'message 0')]");
        $this->clickAndWait("//a[contains(@href,'topic/new')]");
        
        $this->type("//input[@name='subject']","My First Discussion");
        $this->typeInFrame("//iframe", "message 1");
        $this->type("//input[@name='name']", "Mr. Person");
        $this->submitForm();
        $this->ensureGoodMessage();
        
        $invite_url = $this->getAttribute("//div[@class='good_messages']//p//a@href");
        
        $this->mouseOver("//h3[contains(text(),'My First Discussion')]");
        $this->mouseOver("//p[contains(text(),'message 1')]");
        $this->mouseOver("//a[contains(text(),'Mr. Person')]");
        $this->clickAndWait("//a[contains(@href,'testorg/discussions')]");
        $this->mouseOver("//span[@class='feed_snippet' and contains(text(),'message 0')]");
        $this->clickAndWait("//span[@class='feed_snippet' and contains(text(),'message 1')]");
        
        // test add new message
        $this->clickAndWait("//a[contains(@href,'add_message')]");
        $this->typeInFrame("//iframe", "message 2");
        $this->mouseOver("//a[@id='content_html0_image']"); // logged in user can embed images                
        $this->assertEquals("Mr. Person", $this->getValue("//input[@name='name']"));
        $this->type("//input[@name='name']", "Ms. Person");
        $this->submitForm();
        $this->ensureGoodMessage();
                
        $this->mouseOver("//p[contains(text(),'message 2')]");
        $this->mouseOver("//p[contains(text(),'message 1')]");
        $this->mouseOver("//a[contains(text(),'Ms. Person')]");
        $this->mouseOver("//a[contains(text(),'Mr. Person')]");

        $url = $this->getLocation();
        
        // test feed items
        $this->open("/pg/feed");
        $this->mouseOver("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'Mr. Person')]");
        $this->mouseOver("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'message 2')]");
        $this->clickAndWait("//div[contains(@class,'feed_post')]//a[contains(@href,'$url')]");

        // test edit discussion subject
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->type("//input[@name='subject']","new subject");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//p[contains(text(),'message 2')]");
                
        // test delete message from edit page       
        $this->mouseOver("//p[contains(text(),'message 1')]");        
        $this->click("//a[contains(@href,'/delete')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->mustNotExist("//p[contains(text(),'message 1')]");    
        $this->mouseOver("//p[contains(text(),'message 2')]");                   
      
        // test delete topic from edit page
        $this->clickAndWait("//a[contains(@href,'discussions/edit')]");
        $this->mouseOver("//span[@class='feed_snippet' and contains(text(),'message 2')]");
        $this->click("//a[contains(@href,'delete=1')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->mustNotExist("//span[@class='feed_snippet' and contains(text(),'message 2')]");
        $this->mouseOver("//span[@class='feed_snippet' and contains(text(),'message 0')]");
                
        // test adding topic as non logged in user
        $this->clickAndWait("//a[contains(@href,'/pg/logout')]");
        $this->open("/testorg");
        $this->clickAndWait("//a[contains(@href,'/testorg/discussions')]");
        $this->clickAndWait("//a[contains(@href,'/topic/new')]");
        $this->type("//input[@name='subject']","My Second Discussion");
        $this->typeInFrame("//iframe", "message 3");
        $this->mustNotExist("//a[@id='content_html0_image']"); // anonymous user cannot embed images
        $this->type("//input[@name='name']", "Somebody");
        $this->type("//input[@name='location']", "Dar es Salaam");
        $this->submitForm();
        $this->submitFakeCaptcha();
        $this->ensureGoodMessage();
        $this->mouseOver("//h3[contains(text(),'My Second Discussion')]");
        $this->mouseOver("//p[contains(text(),'message 3')]");
        $this->mouseOver("//strong[contains(text(),'Somebody')]");
        $this->mouseOver("//strong[contains(text(),'Dar es Salaam')]");
        
        // test email notifications
        $url = $this->getLocation();      
        $email = $this->getLastEmail("Somebody added a new discussion");
        $this->assertContains($url, $email);
        $this->assertContains('message 3', $email);
        $this->assertContains('Somebody', $email);
        $this->assertContains('My Second Discussion', $email);
        
        $replyTo = $this->getReplyTo($email);
        
        // test replying to email notifications
        $this->receiveMail(array(
            'to' => $replyTo,
            'from' => 'Email Person <foo@nowhere.com>',
            'subject' => "Re: Somebody added a new discussion",
            'body' => "Thanks for your input!

On July 23, 1953, Envaya wrote:
> blah blah blah
> blah blah blah
> blah blah blah"
        ));
        $this->open($url);
        $this->mouseOver("//strong[contains(text(),'Email Person (via email)')]");
        $this->mouseOver("//div[@class='message_content' and contains(text(),'Thanks for your input!')]");
        $this->mustNotExist("//div[@class='message_content' and contains(text(),'1953')]");
        $this->mustNotExist("//div[@class='message_content' and contains(text(),'blah')]");        
        
        // test signed tags
        
        $badReplyTo = str_replace('@','x@', $replyTo);
        $this->receiveMail(array(
            'to' => $badReplyTo,
            'from' => 'Evil Person <foo@nowhere.com>',
            'subject' => "Re: Somebody added a new discussion",
            'body' => "Evil message"
        ));
        $this->open($url);
        $this->mustNotExist("//strong[contains(text(),'Evil Person')]");
        $this->mustNotExist("//div[@class='message_content' and contains(text(),'Evil message')]");        
        
        $this->receiveMail(array(
            'to' => $replyTo,
            'from' => 'foo@nowhere.com',
            'subject' => "Re: Somebody added a new discussion",
            'body' => "Here is another message
With some more stuff

---------
Subject: alkdjalksdjalsjd
sdsdf
sdsdf"
        ));                
        
        $this->open($url);
        $this->mouseOver("//div[@class='message_content' and contains(text(),'Here is another message')]");        
        $this->mustNotExist("//strong[contains(text(),'foo@nowhere.com')]");
        $this->mustNotExist("//div[@class='message_content' and contains(text(),'sdsdf')]");
        $this->mustNotExist("//div[@class='message_content' and contains(text(),'---')]");                
        
        // test adding message as non logged in user
        $this->clickAndWait("//a[contains(@href,'add_message')]");
        $this->typeInFrame("//iframe", "message 4");
        $this->assertEquals("Somebody", $this->getValue("//input[@name='name']"));
        $this->assertEquals("Dar es Salaam", $this->getValue("//input[@name='location']"));
        $this->submitForm();
        // no captcha necessary this time
        $this->ensureGoodMessage();
        $this->mouseOver("//h3[contains(text(),'My Second Discussion')]");
        $this->mouseOver("//p[contains(text(),'message 3')]");
        $this->mouseOver("//p[contains(text(),'message 4')]");
        
        $url = $this->getLocation();        
        $email = $this->getLastEmail("Somebody added a new message");
        $this->assertContains($url, $email);
        $this->assertContains('message 4', $email);
        $this->assertContains('Somebody', $email);
        $this->assertContains('My Second Discussion', $email);        
        
        // test no feed items from deleted or anonymous messages
        $this->open("/pg/feed");
        $this->mouseOver("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'message 0')]");        
        $this->mustNotExist("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'Mr. Person')]");
        $this->mustNotExist("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'message 2')]");
        $this->mustNotExist("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'message 3')]");
        $this->mustNotExist("//div[contains(@class,'feed_post')]//div[@class='feed_snippet' and contains(text(), 'message 4')]");

        // test deleting own message as anonymous user
        $this->setUrl($url);
        $this->mouseOver("//p[contains(text(),'message 3')]");
        $this->click("//a[contains(@href,'/delete')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->mustNotExist("//p[contains(text(),'message 3')]");
             
        $this->click("//a[contains(@href,'/delete')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->mustNotExist("//p[contains(text(),'message 4')]");
        
        $this->mustNotExist("//a[contains(@href,'/delete')]");
        $this->mouseOver("//a[contains(@href,'reply_to')]");
        
        // site admin can delete all messages
        $this->click("//a[contains(@href,'login=1')]");
        $this->login("testorg","testtest");        
        for ($i = 0; $i < 2; $i++)
        {
            $this->click("//a[contains(@href,'/delete')]");
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
            $this->ensureGoodMessage();        
        }
        $this->mustNotExist("//a[contains(@href,'/delete')]");
        
        $this->mustNotExist("//a[@class='discussionTopic']//span[contains(text(),'My First Discussion')]");
        $this->mustNotExist("//a[@class='discussionTopic']//span[contains(text(),'My Second Discussion')]");
        $this->clickAndWait("//a[@class='discussionTopic']//span[contains(text(),'My Zeroth Discussion')]");
                
        // test adding topic, message as other organization                
    }
    
    function deleteDiscussions()
    {
        while (true)
        {
            try
            {
                $this->click("//a[contains(@href,'delete=1')]");
            }
            catch (Exception $ex)
            {
                break;
            }
            
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
            $this->ensureGoodMessage();
        }

        try
        {
            $this->click("//button[@id='widget_delete']");
        }
        catch (Exception $ex)
        {
            return;
        }        
        $this->getConfirmation();        
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();            
    }
}