<?php

class ExternalFeedTest extends SeleniumTest
{
    public function test()
    {        
        $this->open('/pg/login');
        $this->login('testposter7', 'testtest');
        $this->ensureGoodMessage();

        // remove existing external feeds
        $this->clickAndWait("//a[contains(@href,'news/edit')]");
        
        $this->removeExternalFeeds();
        
        // remove existing news updates
        $this->deleteNewsUpdates();
    
        // remove existing links from homepage
        $this->open('/testposter7/page/home/edit');
        $this->clickAndWait("//a[contains(text(),'Other Websites')]");
        $this->removeWebsiteLinks();        

        // add links on homepage        

        // empty url
        $this->click("//button");
        $this->getAlert();
        
        // various invalid URLs
        $this->type("//input[@id='url']", 'kalwjflkaejflkajewf');
        $this->click("//button");
        $this->assertContains("not allowed", $this->retry('getAlert'));

        $this->type("//input[@id='url']", '127.0.0.1');
        $this->click("//button");
        $this->assertContains("not allowed", $this->retry('getAlert'));

        $this->type("//input[@id='url']", 'example.com:81/foo');
        $this->click("//button");
        $this->assertContains("not allowed", $this->retry('getAlert'));
        
        $this->type("//input[@id='url']", 'ftp://example.com/');
        $this->click("//button");
        $this->assertContains("not allowed", $this->retry('getAlert'));
        
        // add link without feed. add missing http://
        $this->type("//input[@id='url']", 'example.com');
        sleep(1);
        $this->click("//button");
        $this->retry('click', array("//input[@value='OK']"), 20);
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->assertContains('Example domains', $this->getText("//a[contains(@href,'http://example.com')]"));
       
        // todo: test local addresses can be added, but not as feeds
        
        // add link with feed, include on news page
        
        $this->type("//input[@id='url']", 'http://www.bbc.co.uk/swahili/');
        $this->click("//button");
        
        $this->retry('mouseOver', array("//div[@class='modalBody']//input[@type='checkbox']"));
        $this->click("//input[@value='OK']");        
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->assertContains('Mwanzo', $this->getText("//a[contains(@href,'http://www.bbc.co.uk/swahili/')]"));
        
        // add link with feed, don't include on news page
        
        $this->type("//input[@id='url']", 'http://twitter.com/bill_westerly');
        $this->click("//button");
        
        $this->retry('uncheck', array("//div[@class='modalBody']//input[@type='checkbox']"));
        $this->click("//input[@value='OK']");        
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->assertContains('Twitter', $this->getText("//a[contains(@href,'http://twitter.com/bill_westerly')]"));
                
        // add and delete link
        $this->type("//input[@id='url']", 'http://example.com/foo');
        $this->click("//button");        
        $this->retry('mouseOver', array("//input[@value='OK']"));        
        $this->mustNotExist("//div[@class='modalBody']//input[@type='checkbox']");
        $this->click("//input[@value='OK']");
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();        
        
        $this->click("//tr[.//a[contains(@href,'foo')]]//a[@class='gridDelete']");            
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->mustNotExist("//a[contains(@href,'foo')]");
        
        // ensure links show up correctly on homepage
        $this->submitForm("//button//span[contains(text(),'Publish')]");
        $this->assertContains('Twitter', $this->getText("//a[contains(@href,'http://twitter.com/bill_westerly')]"));
        $this->assertContains('Mwanzo', $this->getText("//a[contains(@href,'http://www.bbc.co.uk/swahili/')]"));
        $this->assertContains('Example domains', $this->getText("//a[contains(@href,'http://example.com')]"));
        $this->mustNotExist("//a[contains(@href,'foo')]");
        
        // view news page, feed items should be included properly
        $this->retry('checkNews');
        $this->mustNotExist("//a[contains(@href,'twitter.com')]");
        $this->mustNotExist("//a[contains(@href,'facebook.com')]");        
        
        // edit news page, add facebook link
        $this->open('/testposter7/page/news/edit');
        $this->mustNotExist("//a[contains(@href,'twitter.com')]");
        
        $this->retry('type', array("//input[@id='feed_url']", "facebook.com/envaya"));
        $this->click("//form[@id='feed_form']//button");
        $this->retry('uncheck', array("//div[@class='modalBody']//input[@type='checkbox']"), 25);
        $this->click("//input[@value='OK']");   
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();        
        
        // edit news page, add twitter link
        $this->type("//input[@id='feed_url']", "twitter.com/envaya");
        $this->click("//form[@id='feed_form']//button");
        $this->retry('click', array("//input[@value='OK']"), 25);   
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();        
        
        // redirect to home page to add twitter link
        $this->retry('type', array("//div[@class='modalBody']//input[@type='text']", "Envaya Twitter"));
        $this->click("//input[@value='OK']"); 
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();

        // can't add any more feeds
        $this->open('/testposter7/page/news/edit');
        $this->mustNotExist("//input[@id='feed_url']");
        
        // delete unwanted feed item and delete posts
        $this->click("//tr[.//a[contains(@href,'bbc')]]//a[@class='gridDelete']");                            
        $this->retry('click', array("//div[@class='modalBody']//a[contains(text(),'Remove')]"));
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();        
        
        // check posts are deleted
        $this->open("/testposter7/news");
        $this->mustNotExist("//a[contains(@href,'http://www.bbc.co.uk/swahili/habari/')]");
        
        $this->retry('checkFacebook');
        $this->retry('checkTwitter');
        
        // edit news page, add link without rss, add to home page
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->type("//input[@id='feed_url']", "example.com/bar");
        $this->click("//form[@id='feed_form']//button");
        $this->retry('click', array("//input[@value='OK']"));   
        $this->waitForPageToLoad(10000);
        $this->retry('type', array("//div[@class='modalBody']//input[@type='text']", "Example Title"));
        $this->click("//input[@value='OK']"); 
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        
        // test home page links are correct
        $this->open('/testposter7');        
        
        $this->assertContains('Twitter', $this->getText("//a[contains(@href,'http://twitter.com/bill_westerly')]"));
        $this->assertContains('Mwanzo', $this->getText("//a[contains(@href,'http://www.bbc.co.uk/swahili/')]"));
        $this->assertContains('Envaya Twitter', $this->getText("//a[contains(@href,'http://twitter.com/envaya')]"));
        $this->assertContains('Example Title', $this->getText("//a[contains(@href,'http://example.com/bar')]"));
        $this->mustNotExist("//a[contains(@href,'facebook.com')]");
        
    }
    
    function checkNews()
    {        
        $this->clickAndWait("//div[@id='site_menu']//a[contains(@href,'news')]");
        $this->mouseOver("//a[contains(@href,'http://www.bbc.co.uk/swahili/habari/')]");
    }

    function checkTwitter()
    {       
        $this->clickAndWait("//div[@id='site_menu']//a[contains(@href,'news')]");
        $this->mouseOver("//a[contains(@href,'http://twitter.com/Envaya/status')]");
    }

    function checkFacebook()
    {     
        $this->clickAndWait("//div[@id='site_menu']//a[contains(@href,'news')]");
        $this->mouseOver("//a[contains(text(),'via Facebook')]");
    }
    
    function deleteNewsUpdates()
    {
        while (true)
        {
            try
            {
                $this->click("//a[contains(@href,'delete=1')]");
            }
            catch (Exception $ex) { return; }
            
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
            $this->ensureGoodMessage();
        }    
    }
    
    function removeWebsiteLinks()
    {
        while (true)
        {
            try
            {
                $this->click("//a[@class='gridDelete']");
            }
            catch (Exception $ex) { return; }
            
            $this->waitForPageToLoad(10000);
            $this->ensureGoodMessage();
        }
    
    }
    
    function removeExternalFeeds()
    {
        while (true)
        {
            try
            {
                $this->click("//form[@id='feed_form']//a[@class='gridDelete']");
            }
            catch (Exception $ex) { return; }

            try
            {
                $this->click("//div[@class='modalBody']//a[contains(text(),'Remove')]");
            }
            catch (Exception $ex) {}
            
            $this->waitForPageToLoad(10000);
            $this->ensureGoodMessage();
        }
    }
}
