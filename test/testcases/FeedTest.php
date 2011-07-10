<?php

class FeedTest extends WebDriverTest
{
    private $post_content;

    public function test()
    {        
        $this->open("/");
        
        $this->_testCreateFeedItems();
        $this->_checkFeedItems();
    }
    
    public function _testCreateFeedItems()
    {
        for ($i = 0; $i < 22; $i++)
        {
            $this->open("/pg/login");
    
            $this->waitForElement("//input[@name='username']");
            $this->login("testposter$i", 'testtest');            
            $this->retry('ensureGoodMessage', array('Welcome'));
            
            $this->typeInFrame("//iframe", "test post $i");
            $this->submitForm();
            $this->retry('ensureGoodMessage', array('successfully'));

            if ($i >= 20)
            {   
                // multiple news updates from same org should be collapsed
                for ($j = 0; $j < 2; $j++)
                {
                    $this->click("//a[contains(@href,'dashboard')]");
                    $this->waitForElement('//iframe');
                    $this->typeInFrame("//iframe", "another post $i.$j");
                    $this->submitForm();
                    $this->retry('ensureGoodMessage', array('successfully'));
                }                
            }
                        
            $this->logout();
        }
    }
    
    public function _checkFeedItems()
    {
        $this->open("/pg/feed");
        
        $this->waitForElement("//a[contains(@href,'testposter21')]");
        
        // multiple posts should be collapsed
        $this->mustNotExist("//a[contains(@href,'testposter21/post/')]");
        $this->mustNotExist("//a[contains(@href,'testposter20/post/')]");
        
        $this->mouseOver("//a[contains(@href,'testposter20') and @class='feed_org_name']");
        $this->mustNotExist("//a[contains(@href,'testposter20') and @class='feed_org_name'][2]");
        $this->mouseOver("//a[contains(@href,'testposter20/news')]");
        $this->mouseOver("//a[contains(@href,'testposter21/news')]");
        
        $this->mouseOver("//a[contains(@href,'testposter14')]");
        $this->mustNotExist("//a[contains(@href,'testposter0')]");
        
        $this->click("//a[@id='load_more_link']");
        
        $this->waitForElement("//a[contains(@href,'testposter0')]");
        
        $this->mouseOver("//a[contains(@href,'testposter21')]");
        $this->mouseOver("//a[contains(@href,'testposter14')]");
        $this->mouseOver("//a[contains(@href,'testposter1')]");
    }

}