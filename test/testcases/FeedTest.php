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
        for ($i = 13; $i <= 21; $i++)
        {
            $this->open("/pg/login");
    
            $this->waitForElement("//input[@name='username']");
            $this->login("testposter$i", 'asdfasdf');            
            $this->retry('ensureGoodMessage');
            
            $this->setTinymceContent("test post $i");
            $this->submitForm();
            $this->retry('ensureGoodMessage', array('successfully'));

            if ($i >= 20)
            {   
                // multiple news updates from same org should be collapsed
                for ($j = 0; $j < 2; $j++)
                {
                    $this->click("//a[contains(@href,'dashboard')]");
                    $this->waitForElement('//iframe');
                    $this->setTinymceContent("another post $i.$j");
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
        
        $rss_file = $this->xpath("//link[@type='application/rss+xml']")->getAttribute("href");
        
        $this->waitForElement("//a[contains(@href,'testposter21')]");
        
        // multiple posts should be collapsed
        $this->mustNotExist("//a[contains(@href,'testposter21/post/')]");
        $this->mustNotExist("//a[contains(@href,'testposter20/post/')]");
        
        $this->mouseOver("//a[contains(@href,'testposter20') and @class='feed_org_name']");
        $this->mustNotExist("//a[contains(@href,'testposter20') and @class='feed_org_name'][2]");
        $this->mouseOver("//a[contains(@href,'testposter20/news')]");
        $this->mouseOver("//a[contains(@href,'testposter21/news')]");
        
        $this->mouseOver("//a[contains(@href,'testposter16')]");
        $this->mustNotExist("//a[contains(@href,'testposter15')]");
        
        $this->click("//a[@id='load_more_link']");
        
        $this->waitForElement("//a[contains(@href,'testposter15')]");
        
        $this->mouseOver("//a[contains(@href,'testposter21')]");
        $this->mouseOver("//a[contains(@href,'testposter14')]");
        $this->mouseOver("//a[contains(@href,'testposter13')]");
        
        $rss = file_get_contents($rss_file);
        
        Zend_Loader::loadClass('Zend_Feed_Reader');
        
        $feed = Zend_Feed_Reader::importString($rss);
        
        $this->assertContains("/pg/feed", $feed->getLink());
        $this->assertContains("Latest updates", $feed->getTitle());        
        $this->assertEquals(count($feed), 6);
        
        foreach ($feed as $item)
        {
            $this->assertContains("/post/", $item->getLink());
        }
    }

}