<?php

class EnvayaSiteTest extends SeleniumTest
{
    public function test()
    {       
        $this->_testBrowseMap();
        $this->_testContactForm();
    }
    
    private function _testBrowseMap()
    {
        $this->open("/pg/browse");
        
        $this->waitForMapMarker();        
        
        $this->click("//div[@class='mapMarker']");
        $this->click("//div[@class='mapBucketControls']//a"); // zoom in
        
        $this->waitForMapMarker();        
        
        $this->assertFalse($this->isVisible("//div[@id='infoOverlay']"));
        
        $this->click("//div[@class='mapMarker']");
        
        $this->assertTrue($this->isVisible("//div[@id='infoOverlay']"));
        $this->clickAndWait("//div[@id='infoOverlay']//a[@class='mapOrgLink']");        
        
        $this->clickAndWait("//a[contains(@href,'/pg/browse/?lat')]"); // assume map link on org home page
        
        $this->waitForMapMarker();        
        
        $this->clickAndWait("//a[contains(@href,'list=1')]");
        
        $this->assertTrue($this->isElementInPagedList("//a[contains(@href,'testorg')]"));
        
        $this->select("//select[@id='sectorList']","Health");
        $this->waitForPageToLoad(10000);

        $this->assertFalse($this->isElementInPagedList("//a[contains(@href,'testorg')]"));        
        
        $this->select("//select[@id='sectorList']","Education");
        $this->waitForPageToLoad(10000);
        
        $this->assertTrue($this->isElementInPagedList("//a[contains(@href,'testorg')]"));
                
        $this->clickAndWait("//a[contains(@href,'list=0')]");
        $this->assertEquals($this->getSelectedLabel("//select[@id='sectorList']"), "Education");
        
        $this->waitForMapMarker();               
        
        // test map updates when scrolling
        // testorg should be at -6.140555,35.551758, and the test assumes that there are no orgs immediately to the east
        $this->open("/pg/browse/?lat=-6.14055&long=35.5525&zoom=20"); // testorg is 2 clicks out of screen to the west
        sleep(1);
        $this->mustNotExist("//div[@class='mapMarker']");
        
        $this->retry('click', array("//div[@title='Pan left']"));
        sleep(1);
        $this->mustNotExist("//div[@class='mapMarker']");
        
        $this->click("//div[@title='Pan left']");        
        $this->waitForMapMarker();        
        
        // test map updates when zooming
        $this->open("/pg/browse/?lat=-6.14055&long=35.5523&zoom=20"); 
        sleep(1);
        $this->mustNotExist("//div[@class='mapMarker']");
        
        $this->retry('click', array("//div[@title='Zoom out']"));        
        $this->waitForMapMarker();        
        
        $this->assertTrue($this->isVisible("//div[@class='mapMarker']"));
        
        $this->click("//div[@class='mapMarker']");
        $this->mouseOver("//div[@id='infoOverlay']//a[@class='mapOrgLink' and contains(@href,'testorg')]");                
        
        $this->assertTrue($this->isVisible("//div[@id='infoOverlay']"));        
        $this->click("//div[@title='Pan left']");         // should close the overlay
        sleep(1);
        $this->assertFalse($this->isVisible("//div[@id='infoOverlay']"));
        
        // test map updates when changing sector
        $this->select("//select[@id='sectorList']","Health");
        sleep(1);
        $this->mustNotExist("//div[@class='mapMarker']");
        
        $this->select("//select[@id='sectorList']","Education");
        $this->retry('mouseOver', array("//div[@class='mapMarker']"));                
    }    
    
    private function waitForMapMarker()
    {
        $this->retry('mouseOver', array("//div[@class='mapMarker']"));        
    }
    
    private function _testContactForm()
    {
        $this->open("/envaya/contact");
        $this->type("//textarea[@name='message']", "contact message");
        $this->type("//input[@name='name']", "contact name");
        $this->type("//input[@name='email']", "nobody+bar@nowhere.com");
        $this->submitForm();
        $this->ensureGoodMessage();

        $email = $this->getLastEmail("User feedback");

        $this->assertContains("contact message",$email);
        $this->assertContains('contact name', $email);
        $this->assertContains('nobody+bar@nowhere.com', $email);
    }
}
