<?php

class TranslateTest extends SeleniumTest
{
    function test()
    {
        $this->open("/tr/admin/tl");
        $this->login('testadmin','testtest');

        if ($this->isElementPresent("//button[@id='widget_delete']"))
        {
            $this->submitForm("//button[@id='widget_delete']");
            $this->open("/tr/admin/tl");
        }
        
        // create test language
        $this->type("//input[@name='name']", "Test Language");
        $this->check("//input[@value='comment']");
        $this->check("//input[@value='default']");
        $this->uncheck("//input[@value='date']");        
        $this->uncheck("//input[@value='admin']");
        $this->uncheck("//input[@value='network']");
        $this->submitForm();
        $this->ensureGoodMessage();
        
        // clean up from previous tests
        $this->clickAndWait("//a[contains(@href,'/tr/tl/module/comment')]");        
        $this->clickAndWait("//a[contains(@href,'anonymous')]");        
        $this->deleteAllTranslations();
        $this->deleteAllComments();
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        
        // navigate language while logged out
        $this->open("/tr");
        $this->clickAndWait("//a[contains(@href,'/tr/tl')]");        
        $this->mouseOver("//a[contains(@href,'/tr/tl/module/comment')]");
        $this->mouseOver("//a[contains(@href,'/tr/tl/module/default')]");
        $this->mustNotExist("//a[contains(@href,'/tr/tl/module/admin')]");
        $this->mustNotExist("//a[contains(@href,'/tr/tl/module/network')]");
        $this->mustNotExist("//a[contains(@href,'/tr/tl/module/date')]");
        
        $this->clickAndWait("//a[contains(@href,'/tr/tl/module/comment')]");
        
        $this->clickAndWait("//a[contains(@href,'anonymous')]");
        $this->mouseOver("//td//div[contains(text(),'Anonymous')]");
        
        // register for individual account
        $this->clickAndWait("//a[contains(@href,'/pg/register')]");
        $this->type("//input[@name='name']", "Test Translator");
        
        $username = "selenium".time();
        
        $this->type("//input[@name='username']", $username);
        $this->type("//input[@name='password']", 'password');
        $this->type("//input[@name='password2']", 'password2');
        $this->type("//input[@name='email']", 'nobody@nowhere.com');
        $this->type("//input[@name='phone']", '555-1212');
        $this->submitForm();
        $this->ensureBadMessage();
        
        $this->type("//input[@name='password2']", 'password');
        $this->submitForm();
        $this->submitFakeCaptcha();
        $this->ensureGoodMessage();
        
        // add translation
        $this->mouseOver("//td//div[contains(text(),'Anonymous')]");
        $this->deleteAllTranslations();
        $value = 'sidfuaoewir uaoiwuroiaw';
        
        $this->type("//input[@name='value']", $value);        
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//td[contains(text(),'$value')]");
        
        // add comment
        $this->click("//a[contains(@href,'toggleAddComment')]");
        $this->type("//textarea[@name='content']", "comment one");
        $this->submitForm("//form[contains(@action,'add_comment')]//button");
        $this->mouseOver("//div[@class='comment' and contains(text(),'comment one')]");
        $this->ensureGoodMessage();

        // add second comment
        $this->click("//a[contains(@href,'toggleAddComment')]");
        $this->type("//textarea[@name='content']", "comment two");
        $this->submitForm("//form[contains(@action,'add_comment')]//button");
        $this->ensureGoodMessage();        
        $this->mouseOver("//div[@class='comment' and contains(text(),'comment one')]");
        $this->mouseOver("//div[@class='comment' and contains(text(),'comment two')]");
        
        // delete first comment
        $this->click("//div[@class='comment']//span[@class='admin_links']//a");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        $this->mustNotExist("//div[@class='comment' and contains(text(),'comment one')]");
        $this->mouseOver("//div[@class='comment' and contains(text(),'comment two')]");
                
        // add comment in all languages
        $this->click("//a[contains(@href,'toggleAddComment')]");
        $this->type("//textarea[@name='content']", "comment three");
        $this->select("//select[@name='scope']", "All languages");
        $this->submitForm("//form[contains(@action,'add_comment')]//button");
        $this->ensureGoodMessage();
        $this->mouseOver("//div[@class='comment' and contains(text(),'comment two')]");
        $this->mouseOver("//div[@class='comment' and contains(text(),'comment three')]");
                
        // add second translation
        $value2 = 'akjfdakjdsh alkewjf alkewj';        
        
        // test voting
        $this->type("//input[@name='value']", $value2);        
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//td[contains(text(),'$value2')]");        
        $this->mouseOver("//td[contains(text(),'$value')]");
        $this->mustNotExist("//a[contains(@href,'delta=1')]");
        $this->clickAndWait("//tr[.//td[contains(text(),'$value2')]]//a[contains(@href,'delta=-1')]");
        $this->waitForElement("//strong[contains(text(),'0')]");
        $this->clickAndWait("//tr[.//td[contains(text(),'$value2')]]//a[contains(@href,'delta=-1')]");
        $this->waitForElement("//strong[contains(text(),'-1')]");
        
        // test latest non-negative score is shown on module page
        $this->clickAndWait("//h2//a[contains(@href,'/module/comment')]");
        $this->mouseOver("//td[contains(text(),'$value')]");
        $this->mustNotExist("//td[contains(text(),'$value2')]");
        
        $this->clickAndWait("//a[contains(@href,'anonymous')]");
        
        $this->clickAndWait("//tr[.//td[contains(text(),'$value2')]]//a[contains(@href,'delta=1')]");
        $this->waitForElement("//strong[contains(text(),'0')]");

        $this->clickAndWait("//h2//a[contains(@href,'/module/comment')]");
        $this->mouseOver("//td[contains(text(),'$value2')]");
        $this->mustNotExist("//td[contains(text(),'$value')]");        
        
        // test filtering
        $this->select("//select[@name='status']", "Has translation");
        $this->submitForm();
        $this->mouseOver("//a[contains(@href,'anonymous')]");
        $this->mustNotExist("//a[contains(@href,'name_5Fsaid')]");

        $this->select("//select[@name='status']", "Missing translation");
        $this->submitForm();
        $this->mustNotExist("//a[contains(@href,'anonymous')]");
        $this->waitForElement("//a[contains(@href,'name_5Fsaid')]");        
        
        $this->type("//input[@name='q']","deleted");
        $this->submitForm();
        
        $this->mustNotExist("//a[contains(@href,'anonymous')]");
        $this->mustNotExist("//a[contains(@href,'name_5Fsaid')]");        
        $this->clickAndWait("//a[contains(@href,'deleted')]");
        
        // test filters persist through navigation
        $this->waitForElement("//a[contains(@href,'/next')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->mustNotExist("//a[contains(@href,'/next')]");
        
        $this->assertEquals("deleted", $this->getValue("//input[@name='q']"));
        $this->mustNotExist("//a[contains(@href,'anonymous')]");
        $this->mustNotExist("//a[contains(@href,'name_5Fsaid')]");                
        $this->mouseOver("//a[contains(@href,'deleted')]");
        
        $this->type("//input[@name='q']","");
        $this->select("//select[@name='status']", "All");
        $this->submitForm();
        
        $this->mouseOver("//a[contains(@href,'anonymous')]");
        $this->mouseOver("//a[contains(@href,'name_5Fsaid')]");                
        $this->mouseOver("//a[contains(@href,'deleted')]");
        
        // test latest translations
        $this->clickAndWait("//h2//a[contains(@href, '/tr/tl')]");
        $this->clickAndWait("//a[contains(@href, '/tr/tl/latest')]");
        $this->mouseOver("//td[contains(text(),'$value2')]");
        $this->mouseOver("//td[contains(text(),'$value')]");       
        
        // test user stats
        $this->clickAndWait("//a[contains(text(),'$username')]");
        $this->mouseOver("//td[contains(text(),'$value2')]");        
        $this->mouseOver("//td[contains(text(),'$value')]");        
        $this->clickAndWait("//a[contains(@href,'anonymous')]");
        $this->mouseOver("//td[contains(text(),'$value2')]");        
        $this->mouseOver("//td[contains(text(),'$value')]");                
        
        // test latest comments
        $this->open("/tr/tl");
        $this->clickAndWait("//a[contains(@href, '/tr/tl/comments')]");
        $this->mouseOver("//td[contains(text(),'comment two')]");        
        $this->mouseOver("//td[contains(text(),'comment three')]");        
        $this->mustNotExist("//td[contains(text(),'comment one')]");        
        $this->mustNotExist("//td[contains(text(),'$value')]");
        $this->mouseOver("//td[contains(text(),'$value2')]");
        $this->mouseOver("//td[contains(text(),'Anonymous')]");
        $this->mouseOver("//a[contains(@href,'anonymous')]");
    }
    
    function deleteAllTranslations()
    {
        while (true)
        {
            try
            {
                $this->click("//div[@class='admin_links']//a");
            }
            catch (Exception $ex)
            {
                return;
            }
            
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
        }
    }
    
    function deleteAllComments()
    {
        while (true)
        {
            try
            {
                $this->click("//div[@class='comment']//span[@class='admin_links']//a");
            }
            catch (Exception $ex)
            {
                return;
            }
            
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
        }
    }    
}