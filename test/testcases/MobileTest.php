<?php

class MobileTest extends SeleniumTest
{
    public function test()
    {        
        $this->_testMobile();
    }

    private function _testMobile()
    {
        $this->open("/");
        $this->clickAndWait("//a[contains(@href,'view=mobile')]");
        $this->clickAndWait("//div[@id='topbar']//a"); // home page
        $this->clickAndWait("//div[@id='topbar2']//a[contains(@href,'org/feed')]");
        $this->clickAndWait("//div[@id='topbar']//a"); // home page
        $this->clickAndWait("//a[contains(@href,'envaya')]");
        $this->mustNotExist("//table[@class='left_sidebar_table']");
        $this->clickAndWait("//a[contains(@href,'view=default')]");
        $this->mouseOver("//table[@class='left_sidebar_table']");
    }    
}