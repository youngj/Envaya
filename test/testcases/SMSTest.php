<?php

class SMSTest extends WebDriverTest
{
    public function test()
    {       
        $p1 = "14845550133";
        $p2 = "14845550134";
        
        // needs to be between 9am-9pm in local time of phone number for notifications to be sent
        $this->setTimestamp(1319420000); 
        
        $news = $this->config['test:news_phone_number'];
        
        list($res) = $this->sendSMS($p1, $news, "HELP");        
        $this->assertContains("P=publish news", $res);
        
        list($res) = $this->sendSMS($p1, $news, "F test");
        $this->assertContains("[1/2]", $res);
        $this->assertContains("testposter0", $res);
        $this->assertContains('Txt "I [user]" for details', $res);
        
        list($res) = $this->sendSMS($p1, $news, "2");
        $this->assertContains("[2/2]", $res);
        $this->assertContains("testposter21", $res);
        
        list($res) = $this->sendSMS($p1, $news, "more");
        $this->assertContains("No more text available", $res);
        
        list($res) = $this->sendSMS($p1, $news, "next");
        $this->assertContains("No more organizations found", $res);        
        
        list($res) = $this->sendSMS($p1, $news, "F testposter2");
        $this->assertContains("testposter2", $res);
        $this->assertContains("testposter20", $res);
        $this->assertContains("testposter21", $res);
        $this->assertNotContains("testposter1", $res);
        $this->assertNotContains("testorg", $res);        
        
        list($res) = $this->sendSMS($p1, $news, "F asdf");
        $this->assertContains("No organizations found with name 'asdf'", $res);
        
        list($res) = $this->sendSMS($p1, $news, "fn rwanda");
        $this->assertContains("No organizations found near 'rwanda'", $res);
        
        list($res) = $this->sendSMS($p1, $news, "fn tanzania");
        $this->assertContains("testposter0", $res);
        $this->assertContains("testorg", $res);
        
        list($res) = $this->sendSMS($p1, $news, "i testorg");
        $this->assertContains("Test Org", $res);
        $this->assertContains("TZ", $res);
        $this->assertContains("http://{$this->config['domain']}/testorg", $res);
        $this->assertContains("@", $res);
        
        list($res) = $this->sendSMS($p1, $news, "s");
        $this->assertContains('Subscribed to "N testorg"', $res);
        $this->assertContains('Txt "SS" to show all subscriptions', $res);
        
        list($res) = $this->sendSMS($p1, $news, "s testposter1");
        $this->assertContains('Subscribed to "N testposter1"', $res);
        
        list($res) = $this->sendSMS($p1, $news, "ss");
        $this->assertContains('1:N testorg', $res);
        $this->assertContains('2:N testposter1', $res);
        $this->assertContains('"STOP [num]"', $res);
                
        list($res) = $this->sendSMS($p1, $news, "stop 2");
        $this->assertContains('Subscription to "N testposter1" stopped', $res);

        list($res) = $this->sendSMS($p1, $news, "sr 2");
        $this->assertContains('Subscription to "N testposter1" started', $res);
        
        list($res) = $this->sendSMS($p1, $news, "stop 1");
        $this->assertContains('Subscription to "N testorg" stopped', $res);
        
        list($res) = $this->sendSMS($p1, $news, "ss");
        $this->assertNotContains('1:N testorg', $res);
        $this->assertContains('2:N testposter1', $res);                
        
        $this->open("/pg/login");
        $this->login("testposter1","asdfasdf");
        $this->waitForElement("//iframe");
        $this->setTinymceContent(
            "test post 1000 test post 1000 test post 1000 test post 1000 test post 1000 "
            ."test post 1000 test post 1000 test post 1000 test post 1000 test post 1000 "
            ."test post 1000 test post 1000 test post 1000 test post 1000 test post 1000 "
            ."test post 1000 test post 1000 test post 1000 test post 1000 test post 1000 "
            ."test post 1000 test post 1000 test post 1000 test post 1000 test post 1000 end");
        $this->submitForm();
        $this->retry('ensureGoodMessage', array('saved successfully'));
        
        $this->open("/testposter1/dashboard");
        $this->waitForElement("//iframe");
        sleep(1);
        $this->setTinymceContent("test post 1001");
        $this->submitForm();
        $this->retry('ensureGoodMessage', array('saved successfully'));        
        
        $sms = $this->getLastSMS("To: $p1");
        $this->assertContains('testposter1 published news', $sms);                
        $this->assertContains('"N testposter1', $sms);
        $this->assertContains("http://{$this->config['domain']}/testposter1", $sms);
        
        list($res) = $this->sendSMS($p1, $news, "N testposter1");
        $this->assertContains('test post 1001', $res);
        
        list($res) = $this->sendSMS($p1, $news, "next");
        $this->assertContains('test post 1000', $res);
        $this->assertContains('0 comments', $res);
        $this->assertNotContains('end', $res);
        $this->assertContains('MORE', $res);
        
        list($res) = $this->sendSMS($p1, $news, "more");
        $this->assertContains('test post 1000', $res);
        $this->assertNotContains('0 comments', $res);
        $this->assertContains('end', $res);
        $this->assertNotContains('MORE', $res);
        
        list($res) = $this->sendSMS($p1, $news, "name test phone");
        $this->assertContains("Name changed to 'test phone'.", $res);
        
        list($res) = $this->sendSMS($p1, $news, "loc selenium");
        $this->assertContains("Location changed to 'selenium'.", $res);
        
        list($res) = $this->sendSMS($p1, $news, "c this is a test comment!");
        $this->assertContains("Your comment has been published", $res);

        list($res) = $this->sendSMS($p1, $news, "g1");
        $this->assertContains("test phone", $res);
        $this->assertContains("selenium", $res);
        $this->assertContains("this is a test comment!", $res);
        

        $email = $this->getLastEmail("nobody@envaya.org");
        $this->assertContains("test phone added a new comment", $email);
        $this->assertContains('this is a test comment!', $email);

        $email = $this->getLastEmail("nobody+p1@envaya.org");
        $this->assertContains("test phone added a new comment", $email);
        $this->assertContains('this is a test comment!', $email);
        
        $url = $this->getLinkFromText($email);
        $this->open($url);
        $this->waitForElement("//textarea");
        $this->type("//textarea", "test comment from web");
        $this->submitForm();
        $this->retry('ensureGoodMessage', array('comment has been published'));                
        
        $sms = $this->getLastSMS('added a comment');
        $this->assertContains("To: $p1", $sms);                
        $this->assertContains('"N testposter1', $sms);
        
        list($res) = $this->sendSMS($p1, $news, "l sw");        
        $this->assertContains("Lugha imebadilishwa.", $res);
        
        list($res) = $this->sendSMS($p1, $news, "help");        
        $this->assertContains("chapisha maoni", $res);
        
        list($res) = $this->sendSMS($p1, $news, "l en");        
        $this->assertContains("Language changed.", $res);        
        
        list($res) = $this->sendSMS($p2, $news, "ss");
        $this->assertContains('You do not have any active subscriptions', $res);
        
        list($res) = $this->sendSMS($p1, $news, "p this is a test of the sms posting interface");        
        $this->assertContains("IN", $res);
        
        list($res) = $this->sendSMS($p1, $news, "login testorg alksdjfalksjfd");        
        $this->assertContains("The password 'alksdjfalksjfd' was incorrect for username 'testorg'.", $res);
        
        list($res) = $this->sendSMS($p1, $news, "login testorg asdfasdf");        
        $this->assertContains("news update has been published", $res);
        
        $url = $this->getLinkFromText($res);
        
        $this->open($url);
        $this->waitForElement("//div[@class='blog_date']//a");
        
        $this->assertContains("this is a test of the sms posting interface", $this->getText("//div[contains(@class,'section_content')]"));
        $this->assertContains("via SMS", $this->getText("//div[@class='blog_date']//a"));
        
        if (!preg_match("#DELETE\s\d+#", $res, $match))
        {
            throw new Exception("Expected DELETE command in SMS reply");
        }
        
        list($res) = $this->sendSMS($p2, $news, $match[0]);        
        $this->assertContains("do not have access", $res);
        
        list($res) = $this->sendSMS($p1, $news, $match[0]);        
        $this->assertContains("deleted successfully", $res); 

        $this->open($url);
        $this->assertNotContains("this is a test of the sms posting interface", $this->getText("//div[contains(@class,'section_content')]"));
                
        list($res) = $this->sendSMS($p2, $news, "what?");        
        $this->assertContains("Unknown command", $res);        
        $this->assertContains("HELP", $res);        
        
        list($res) = $this->sendSMS($p2, $news, "today we planted a lot of trees");        
        $this->assertContains('txt "P"', $res);        
        
        list($res) = $this->sendSMS($p2, $news, "yes");

        list($res) = $this->sendSMS($p2, $news, "login testposter2 asdfasdf");        
        $this->assertContains("news update has been published", $res);
        
        $url = $this->getLinkFromText($res);
        $this->assertContains("testposter2", $url);
        
        list($res) = $this->sendSMS($p1, $news, "p yada yada yada");        
        $this->assertContains("news update has been published", $res);
        
        $url = $this->getLinkFromText($res);
        $this->assertContains("testorg", $url);                     
        
        $this->open($url);
        $this->waitForElement("//div[@class='blog_date']//a");
        $this->assertContains("yada yada yada", $this->getText("//div[contains(@class,'section_content')]"));
        
        list($res) = $this->sendSMS($p1, $news, "u");        
        $this->assertContains("N testorg", $res);                
        $this->assertContains("N testposter1", $res);         
    }       
}