<?php

class SMSTest extends WebDriverTest
{
    public function test()
    {       
        global $TEST_CONFIG;
               
        $p1 = "14845550133";
        $p2 = "14845550134";
        
        $news = $TEST_CONFIG['news_phone_number'];
        
        // detect auto-reply loops
        list($res) = $this->sendSMS($p1, $news, "xxx");        
        $this->assertContains("Unknown command", $res);        
        
        list($res) = $this->sendSMS($p1, $news, "xxx");        
        $this->assertContains("Unknown command", $res);        
        
        list($res) = $this->sendSMS($p1, $news, "xxx");        
        $this->assertContains("Unknown command", $res);        
                
        $res = $this->sendSMS($p1, $news, "xxx");        
        $this->assertEmpty($res);      
                
        list($res) = $this->sendSMS($p1, $news, "HELP");        
        $this->assertContains("P=publish news", $res);
        
        list($res) = $this->sendSMS($p1, $news, "p this is a test of the sms posting interface");        
        $this->assertContains("IN", $res);
        
        list($res) = $this->sendSMS($p1, $news, "login testorg alksdjfalksjfd");        
        $this->assertContains("The password 'alksdjfalksjfd' was incorrect for username 'testorg'.", $res);
        
        list($res) = $this->sendSMS($p1, $news, "login testorg testtest");        
        $this->assertContains("news update has been published", $res);
        
        $url = $this->getLinkFromText($res);
        
        $this->open($url);
        $this->waitForElement("//div[@class='blog_date']//a");
        
        $this->assertContains("this is a test of the sms posting interface", $this->getText("//div[contains(@class,'section_content')]"));
        $this->assertContains("via SMS", $this->getText("//div[@class='blog_date']//a"));
        
        if (!preg_match("#D\s\d+#", $res, $match))
        {
            throw new Exception("Expected D command in SMS reply");
        }
        
        list($res) = $this->sendSMS($p2, $news, $match[0]);        
        $this->assertContains("do not have access", $res);
        
        list($res) = $this->sendSMS($p1, $news, $match[0]);        
        $this->assertContains("deleted successfully", $res); 

        $this->open($url);
        $this->assertContains("Page not found", $this->getTitle());        
        
        list($res) = $this->sendSMS($p2, $news, "what?");        
        $this->assertContains("Unknown command", $res);        
        $this->assertContains("HELP", $res);        
        
        list($res) = $this->sendSMS($p2, $news, "today we planted a lot of trees");        
        $this->assertContains('txt "P"', $res);        
        
        list($res) = $this->sendSMS($p2, $news, "yes");

        list($res) = $this->sendSMS($p2, $news, "login testposter2 testtest");        
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
    }       
}