<?php

class EditorTest extends SeleniumTest
{    
    public function test()
    {        
        $this->open('/pg/login');
        $this->login('testorg','testtest');
        
        $this->click("//a[@class='hideMessages']");
        sleep(1);
        
        $this->open("/testorg/dashboard");
        $this->mustNotExist("//a[@class='hideMessages']");
        
        // clean up pages from any previous aborted/failed tests
        $testPageXpath = "//a//span[contains(text(), 'Test Page')]";
        while ($this->isElementPresent($testPageXpath))
        {
            $this->clickAndWait($testPageXpath);
            $this->click("//button[@id='widget_delete']");
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);              
            $this->ensureGoodMessage();
            $this->open('/testorg/dashboard');
        }
        
        // test draft of new page
        $this->clickAndWait("//a[contains(@href,'add_page')]");
        
        $pageName = 'testpage'.time();
        
        $this->type("//input[@name='title']", "Test Page");
        $this->type("//input[@name='widget_name']", $pageName);
        $this->typeInFrame("//iframe", "test content 1");
        $this->clickAndWait("//a[@id='content_html0_save']");
        
        $this->assertEquals("Test Page", $this->getValue("//input[@name='title']"));

        $this->retry('selectFrame', array("//iframe"));
        $this->mouseOver("//p[contains(text(),'test content 1')]");
        $this->selectFrame("relative=top");        
        
        $this->assertEquals("Test Page", $this->getValue("//input[@name='title']"));
        
        // test draft not published
        $this->open("/testorg/page/$pageName");
        $this->retry('mouseOver', array("//h3[contains(text(),'Page not found')]"));
        $this->mustNotExist("//p[contains(text(),'test content 1')]");
        
        // test publishing page works
        $this->open("/testorg/page/$pageName/edit");
        $this->typeInFrame("//iframe", "test content 2");
        $this->submitForm();
        
        $this->ensureGoodMessage();
        $this->mouseOver("//p[contains(text(),'test content 2')]");
        $this->mustNotExist("//p[contains(text(),'test content 1')]");
        
        $this->open("/testorg/page/$pageName");
        $this->mouseOver("//p[contains(text(),'test content 2')]");
        
        $this->clickAndWait("//div[@id='edit_submenu']//a");

        // test saving draft doesn't update public view of page
        $this->retry('selectFrame', array("//iframe"));
        $this->mouseOver("//p[contains(text(),'test content 2')]");
        $this->selectFrame("relative=top");        
        
        $this->typeInFrame("//iframe", "test content 3");
        $this->click("//a[@id='content_html0_save']");
        $this->retry('mouseOver', array("//span[@id='saved_message' and contains(text(),'Changes saved')]"));
        
        $this->open("/testorg/page/$pageName");
        $this->mouseOver("//p[contains(text(),'test content 2')]");
        $this->mustNotExist("//p[contains(text(),'test content 3')]");
        
        // test publishing updates public view of page
        $this->open("/testorg/page/$pageName/edit");
        $this->submitForm();
        $this->mouseOver("//p[contains(text(),'test content 3')]");
        $this->mustNotExist("//p[contains(text(),'test content 2')]");
        
        // test can restore previous version
        $this->open("/testorg/page/$pageName/edit");
        $this->retry('selectFrame', array("//iframe"));
        $this->mouseOver("//p[contains(text(),'test content 3')]");
        $this->selectFrame("relative=top");
        
        $this->click("//a[@id='content_html0_restoredraft']");
        $this->retry('mouseOver', array("//div[@class='revisionLink']//a"));
        $this->mouseOver("//div[@class='revisionLink'][2]//a");
        $this->mustNotExist("//div[@class='revisionLink'][3]//a");
        $this->click("//div[@class='revisionLink'][2]//a");
        
        $this->retry('mouseOver', array("//div[@class='revisionPreview']//p"));
        
        $this->assertContains("test content 2", $this->getText("//div[@class='revisionPreview']//p"));
        $this->click("//input[@value='Restore']");
        $this->retry('selectFrame', array("//iframe"));
        $this->retry('mouseOver', array("//p[contains(text(),'test content 2')]"));
        $this->selectFrame("relative=top");
        $this->submitForm();
        $this->mouseOver("//p[contains(text(),'test content 2')]");
        $this->mustNotExist("//p[contains(text(),'test content 3')]");        
        
        // delete page
        $this->open("/testorg/page/$pageName/edit");
        $this->click("//button[@id='widget_delete']");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);        
    }   
}
