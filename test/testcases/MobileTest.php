<?php

class MobileTest extends SeleniumTest
{
    private $post_content;

    public function test()
    {        
        $this->open("/");
        $this->clickAndWait("//a[contains(@href,'view=mobile')]");
        
        $this->_testEdit();
        $this->_testFeed();
        $this->_testLayout();
        $this->_testSearch();
        $this->_testBrowse();
    }

    public function goToMainMenu()
    {
        $this->clickAndWait("//input[@value='Main Menu']");
    }
    
    public function submitForm()
    {
        $this->clickAndWait("//input[@type='submit']");
    }
    
    private function _testLayout()
    {                
        $this->clickAndWait("//div[@id='topbar']//a"); // home page
        $this->clickAndWait("//div[@id='topbar2']//a[contains(@href,'org/feed')]");
        $this->clickAndWait("//div[@id='topbar']//a"); // home page
        $this->clickAndWait("//a[contains(@href,'envaya')]");
        
        $this->mustNotExist("//table[@class='left_sidebar_table']");
        
        // mobile version should not have login button or top bar links on org pages
        $this->mustNotExist("//a[@id='loginButton']"); 
        $this->mustNotExist("//a[contains(@href,'org/browse')]"); 
        
        $this->clickAndWait("//a[contains(@href,'view=default')]");
        
        // make sure normal version has the elements that should be omitted in mobile version
        $this->mouseOver("//table[@class='left_sidebar_table']");        
        $this->mouseOver("//a[@id='loginButton']"); 
        $this->mouseOver("//a[contains(@href,'org/browse')]"); 
        
        $this->clickAndWait("//a[contains(@href,'view=mobile')]");
        
        $this->goToMainMenu();
    }    
           
    private function _testSearch()
    {
        $this->clickAndWait("//a[contains(@href,'org/search')]");     
        $this->type("//input[@name='q']", 'testorg');
        $this->submitForm();
        $this->clickAndWait("//li//a");
        $this->mouseOver("//a[contains(@href,'contact')]");
        $this->clickAndWait("//a[contains(@href,'news')]");        
        $this->mustNotExist("//a[contains(@href,'contact')]");
        $this->clickAndWait("//input[@value='Home']");
        $this->mouseOver("//a[contains(@href,'contact')]");
        
        $this->goToMainMenu();
    }
    
    private function _testBrowse()
    {
        $this->clickAndWait("//a[contains(@href,'org/browse')]");     
        
        $this->assertTrue($this->isElementInPagedList("//a[contains(@href,'testorg')]"));

        $this->clickAndWait("//li//a[contains(@href,'testorg')]");
        $this->mouseOver("//a[contains(@href,'contact')]");
        
        $this->goToMainMenu();
        
        $this->clickAndWait("//a[contains(@href,'org/browse')]");     
        
        $this->clickAndWait("//a[contains(@href,'org/change_browse_view')]");     
        
        $this->select("//select[@name='sector']", "Education");     
        $this->submitForm();
        
        $this->assertTrue($this->isElementInPagedList("//a[contains(@href,'testorg')]"));
        
        $this->clickAndWait("//a[contains(@href,'lang=sw')]");     
        
        $this->assertTrue($this->isElementInPagedList("//a[contains(@href,'testorg')]"));
        
        $this->clickAndWait("//a[contains(@href,'org/change_browse_view')]");     
        
        $this->select("//select[@name='sector']", "Mazingira");     
        $this->submitForm();        
        
        $this->assertFalse($this->isElementInPagedList("//a[contains(@href,'testorg')]"));
        
        $this->clickAndWait("//a[contains(@href,'lang=en')]");     
        
        $this->goToMainMenu();
    }
    
    private function _testEdit()
    {
        $this->clickAndWait("//a[contains(@href,'pg/login')]");     
        $this->type("//input[@name='username']", "testorg");
        $this->type("//input[@name='password']", "testtest");
        $this->submitForm();
        $this->type("//textarea", "This is my mobile test post.");
        $this->submitForm();
        $this->mouseOver("//div[@class='blog_post' and contains(text(), 'mobile test post')]");
        $this->clickAndWait("//a[contains(@href,'/edit')]"); 
        
        $this->post_content = "This is my mobile post ".time();
        
        $this->type("//textarea", $this->post_content);
        $this->submitForm();
        $this->mouseOver("//div[@class='blog_post' and contains(text(), '{$this->post_content}')]");
        $this->goToMainMenu();
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");     
    }
    
    private function _testFeed()
    {
        $this->clickAndWait("//a[contains(@href,'org/feed')]");   
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), '{$this->post_content}')]");        
        
        $this->clickAndWait("//a[contains(@href,'org/change_feed_view')]");     
        
        $this->select("//select[@name='sector']", "Education");     
        $this->submitForm();
        
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), '{$this->post_content}')]");        
        
        $this->clickAndWait("//a[contains(@href,'org/change_feed_view')]");     
        
        $this->select("//select[@name='region']", "Arusha");     
        $this->submitForm();        
        
        $this->mustNotExist("//div[@class='feed_snippet' and contains(text(), '{$this->post_content}')]");        
        
        $this->goToMainMenu();        
    }
}