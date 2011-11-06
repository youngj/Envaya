<?php

class TranslateTest extends SeleniumTest
{
    function test()
    {
        $this->open("/tr/admin/tl");
        $this->login('testadmin','secretpassw0rd');

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
        
        $this->logout();
            
        $this->_testTranslateUserContent();
        $this->_testTranslateInterface();
    }
        
    function _testTranslateUserContent()
    {
        // log in as test org
        $this->open('/pg/login');
        $this->login('testorg', 'asdfasdf');
        
        // publish three news updates in 2 languages
        $this->typeInFrame("//iframe", "translating is a difficult challenge");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");
        $difficult_url = $this->getLocation();
        
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");

        $this->typeInFrame("//iframe", "je, inawezekana kwenda baharini leo?");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//p[contains(text(),'je, inawezekana kwenda baharini leo?')]");
        
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");

        $this->typeInFrame("//iframe", "this is a test of the emergency broadcast system");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//p[contains(text(),'this is a test of the emergency broadcast system')]");
        
        // view news updates without translating
        $this->retry('_testDetectLanguage', array('/testorg/news','Swahili'));
        
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");
        $this->mouseOver("//p[contains(text(),'je, inawezekana kwenda baharini leo?')]");
        $this->mouseOver("//p[contains(text(),'this is a test of the emergency broadcast system')]");        
               
        // translate using google translate
        $this->clickAndWait("//a[contains(@href,'trans=3')]");
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");
        $this->mouseOver("//p[contains(text(),'this is a test of the emergency broadcast system')]");        
        $this->waitForElement("//p[contains(text(),'Is it possible to go to sea today?')]");        
        
        // go back to original version
        $this->clickAndWait("//a[contains(@href,'trans=1')]");
        $this->mouseOver("//p[contains(text(),'je, inawezekana kwenda baharini leo?')]");
        
        // change to kiswahili, translate multiple pieces of text at once
        $this->clickAndWait("//a[contains(@href,'lang=sw')]");
        $this->waitForElement("//div[@id='language']//strong[contains(text(),'Kiswahili')]");
        $this->assertContains("Maeneo ya ukurasa huu ni kwa Kiingereza", $this->getText("//div[@id='translate_bar']"));
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");
        $this->mouseOver("//p[contains(text(),'this is a test of the emergency broadcast system')]");        
        $this->clickAndWait("//a[contains(@href,'trans=3')]");
        
        $this->waitForElement("//p[contains(text(),'je, inawezekana kwenda baharini leo?')]");
        $this->waitForElement("//p[contains(text(),'ni mtihani wa mfumo wa matangazo ya dharura')]");        
        $this->waitForElement("//p[contains(text(),'kutafsiri ni changamoto ngumu')]");        
        
        // edit translation
        
        // selenium 1.0 doesn't handle target=_blank
        $this->open($this->getAttribute("//div[@id='translate_bar']//a[contains(@href,'/tr/page')]@href"));
        
        $this->waitForElement("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'changamoto')]");
        $this->waitForElement("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Bila tafsiri')]");        
        $this->waitForElement("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");        
        
        // next/prev links cycle through multiple items
        
        $this->clickAndWait("//tr//a[contains(text(),'baharini')]");        
        $this->clickAndWait("//a[contains(@href,'/prev')]");
        $this->mouseOver("//div[@class='translation']//p[contains(text(),'difficult')]");        
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->mouseOver("//div[@class='translation']//p[contains(text(),'baharini')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->mouseOver("//div[@class='translation']//p[contains(text(),'emergency')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->mouseOver("//tr//a[contains(text(),'emergency')]");        
        
        // test go directly to translation page when there is only one translation on the page        
        $this->open($difficult_url);
        $this->open($this->getAttribute("//div[@id='translate_bar']//a[contains(@href,'/tr/page')]@href"));
        $this->mouseOver("//div[@class='translation']//p[contains(text(),'difficult')]");        
                
        // save draft                
        $this->retry('selectFrame', array("//iframe"));        
        $this->mouseOver("//p[contains(text(),'kutafsiri ni changamoto ngumu')]");        
        $this->type("//body", "kutafsiri ni rahisi sana");
        $this->selectFrame("relative=top");
        $this->click("//a[@id='content_html0_save']");
        $this->waitForElement("//span[@id='saved_message' and contains(text(),'hifadhiwa')]");
        
        $this->refresh();
        
        $translate_url = $this->getLocation();
        
        // click Restore draft link to restore draft
        $this->retry('selectFrame', array("//iframe"));        
        $this->retry('mouseOver', array("//p[contains(text(),'kutafsiri ni changamoto ngumu')]"));        
        $this->selectFrame("relative=top");
        
        $this->clickAndWait("//table[@class='translateTable']//a[contains(@href,'translation=')]");
        
        $this->retry('selectFrame', array("//iframe"));        
        $this->retry('mouseOver', array("//p[contains(text(),'kutafsiri ni rahisi sana')]"));
        $this->selectFrame("relative=top");
        
        $draft_url = $this->getLocation();
        
        // test each user has their own drafts
        $this->logout();
        $this->open("/pg/login");
        $this->login("testposter1","asdfasdf");
        $this->open($translate_url);
        $this->mustNotExist("//table[@class='translateTable']//a[contains(@href,'translation=')]");
        $this->typeInFrame("//iframe", "sipendi kutafsiri");
        $this->click("//a[@id='content_html0_save']");
        $this->waitForElement("//span[@id='saved_message' and contains(text(),'hifadhiwa')]");        
        
        $this->refresh();
        
        $this->retry('selectFrame', array("//iframe"));        
        $this->retry('mouseOver', array("//p[contains(text(),'kutafsiri ni changamoto ngumu')]"));        
        $this->selectFrame("relative=top");
        
        $this->clickAndWait("//table[@class='translateTable']//a[contains(@href,'translation=')]");

        $this->retry('selectFrame', array("//iframe"));        
        $this->retry('mouseOver', array("//p[contains(text(),'sipendi kutafsiri')]"));        
        $this->selectFrame("relative=top");        
        
        $this->setTimestamp(time());
        
        $this->submitForm();
        $this->ensureGoodMessage("Tafsiri imeongezwa");
        
        // published translations need approval when translated by other user
        $this->open($difficult_url);
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");
        
        $this->logout();
        $this->open("/pg/login");
        $this->login('testorg','asdfasdf');
        $this->open($translate_url);
        $this->clickAndWait("//a[contains(@href,'approval=1')]");
        $this->ensureGoodMessage();
        
        $this->open($difficult_url);
        $this->mouseOver("//p[contains(text(),'sipendi kutafsiri')]");                
        
        // published translations automatically approved when translated by content owner
                
        $this->open($draft_url);
        $this->retry('selectFrame', array("//iframe"));        
        $this->mouseOver("//p[contains(text(),'kutafsiri ni rahisi sana')]");        
        $this->type("//body", "kutafsiri ni rahisi kidogo");        
        $this->selectFrame("relative=top");
        
        $this->setTimestamp(time());
        $this->submitForm();
        $this->ensureGoodMessage("Tafsiri imeongezwa");       
        
        $this->open($difficult_url);
        $this->mouseOver("//p[contains(text(),'kutafsiri ni rahisi kidogo')]");                
        $this->mustNotExist("//p[contains(text(),'sipendi kutafsiri')]");                
        
        // viewer can switch between translated version and original content
        $this->clickAndWait("//a[contains(@href,'trans=1')]");
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");
        $this->clickAndWait("//a[contains(@href,'trans=2')]");
        $this->mouseOver("//p[contains(text(),'kutafsiri ni rahisi kidogo')]");                
                
        // viewer can translate rest using google translate if only some of the content has human translation
        $this->open('/testorg/news');
        $this->mouseOver("//p[contains(text(),'kutafsiri ni rahisi kidogo')]");                
        $this->mouseOver("//p[contains(text(),'this is a test of the emergency broadcast system')]");        
        $this->clickAndWait("//a[contains(@href,'trans=3')]");        
        $this->mouseOver("//p[contains(text(),'kutafsiri ni rahisi kidogo')]");                
        $this->mouseOver("//p[contains(text(),'ni mtihani wa mfumo wa matangazo ya dharura')]");        
        $this->clickAndWait("//a[contains(@href,'trans=1')]");     
        $this->mouseOver("//p[contains(text(),'translating is a difficult challenge')]");        
        $this->mouseOver("//p[contains(text(),'this is a test of the emergency broadcast system')]");        
        
        // test translations appear on /tr/../content, and filters work
        $this->open("/tr/sw?lang=en");
        $this->clickAndWait("//a[contains(@href,'/tr/sw/content')]");
        
        $this->mouseOver("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'sipendi kutafsiri')]");
        $this->mouseOver("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Not translated')]");        
        $this->mouseOver("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");        

        $this->select("//select[@name='status']", "Not translated");
        $this->waitForPageToLoad();
        $this->mustNotExist("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'sipendi kutafsiri')]");
        $this->mouseOver("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Not translated')]");        
        $this->mustNotExist("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");        
        
        $this->select("//select[@name='status']", "Translated");
        $this->waitForPageToLoad();
        $this->mouseOver("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'sipendi kutafsiri')]");
        $this->mustNotExist("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Not translated')]");        
        $this->mouseOver("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");        

        $this->select("//select[@name='status']", "Approved");
        $this->waitForPageToLoad();
        $this->mouseOver("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'sipendi kutafsiri')]");
        $this->mustNotExist("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Not translated')]");        
        $this->mustNotExist("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");                
        
        $this->select("//select[@name='status']", "Unapproved");
        $this->waitForPageToLoad();
        $this->mustNotExist("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'sipendi kutafsiri')]");
        $this->mustNotExist("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Not translated')]");        
        $this->clickAndWait("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");        
        
        // test filters persist through navigation
        $this->clickAndWait("//a[contains(@href,'/tr/sw/content')]");
        
        $this->waitForElement("//select[@name='status']//option[@selected='selected' and @value='unapproved']");
        $this->mustNotExist("//tr[.//a[contains(text(),'difficult')]]//a[contains(text(),'sipendi kutafsiri')]");
        $this->mustNotExist("//tr[.//a[contains(text(),'baharini')]]//span[contains(text(),'Not translated')]");        
        $this->clickAndWait("//tr[.//a[contains(text(),'emergency')]]//a[contains(text(),'dharura')]");          

        // edit content, test translation shows up as stale
        $this->open($difficult_url);
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->typeInFrame("//iframe", "translating is fun");
        $this->submitForm();
        
        $this->open("$difficult_url?lang=sw");
        
        $this->assertContains("Maeneo ya ukurasa huu yametafsiriwa toka Kiingereza, lakini tamko la Kiswahili ni la zamani", 
            $this->getText("//div[@id='translate_bar']"));
        
        $this->mouseOver("//p[contains(text(),'kutafsiri ni rahisi kidogo')]");                
        
        // submit new translation, translation no longer stale
        $this->open($this->getAttribute("//div[@id='translate_bar']//a[contains(@href,'/tr/page')]@href"));
        $this->typeInFrame("//iframe", "nataka kutafsiri");
        $this->submitForm();
        $this->ensureGoodMessage();        
        $this->open($difficult_url);
        $this->mouseOver("//p[contains(text(),'nataka kutafsiri')]");                
        $this->assertContains("Maeneo ya ukurasa huu yametafsiriwa toka Kiingereza kwa Kiswahili", 
            $this->getText("//div[@id='translate_bar']"));
                
        // delete content, test can't view translation anymore
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->submitForm("//button[@id='widget_delete']");
        $this->getConfirmation();
        $this->ensureGoodMessage('futwa');        
        
        $this->open('/tr/sw/content');
        $this->mustNotExist("//tr//a[contains(text(),'kutafsiri')]");
        $this->mouseOver("//tr//a[contains(text(),'dharura')]");             

        $this->open($translate_url);
        $this->ensureBadMessage();
        
        // can't view translations from unapproved orgs, unless admin       
        $this->login("testunapproved","asdfasdf");
        $this->open("/testunapproved/add_page");
        $this->type("//input[@name='title']",'my stuff');
        $this->type("//input[@name='widget_name']",'my-stuff');
        $this->typeInFrame("//iframe",'this text was written by an unapproved organization');
        $this->submitForm();
        $this->mouseOver("//p[contains(text(),'this text was written by an unapproved organization')]");
        $this->open($this->getAttribute("//a[contains(@href,'/tr/page')]@href"));
        
        $this->mouseOver("//tr//a[contains(text(),'unapproved organization')]");
        $this->mouseOver("//tr//a[contains(text(),'my stuff')]");
        $unapproved_url = $this->getLocation();
        $this->logout();
        $this->open("/pg/login");
        $this->login('testorg','asdfasdf');
        $this->open($unapproved_url);
        $this->mouseOver("//tr//td[contains(text(),'hidden')]");
        $this->mustNotExist("//tr//a[contains(text(),'unapproved organization')]");
        $this->mustNotExist("//tr//a[contains(text(),'my stuff')]");
        $this->logout();
        $this->open("/pg/login");
        $this->login('testadmin','secretpassw0rd');
        $this->open($unapproved_url);
        $this->mustNotExist("//tr//td[contains(text(),'hidden')]");
        $this->mouseOver("//tr//a[contains(text(),'unapproved organization')]");
        $this->clickAndWait("//tr//a[contains(text(),'my stuff')]");
                
        // title should use non-html editor        
        $this->type("//input[@name='value']", "vitu <a href='xxx'>vyangu</a>");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->clickAndWait("//h2//a");
        
        // test html is removed
        $this->mouseOver("//a[@class='selected']//span[contains(text(),'vitu vyangu')]");
        $this->mouseOver("//p[contains(text(),'this text was written by an unapproved organization')]");
        
        // change language of original content                
        $this->open("/testorg/dashboard");
        $this->typeInFrame("//iframe", "aslkfdalkewjfalkwejfa;lewkja;ewlkfja;ewlkfjawef aweoif uawe;lfk jaewf");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mustNotExist("//div[@id='translate_bar']");
        
        sleep(1);
        $this->refresh();
        $unknown_url = $this->getLocation();
        
        $this->mustNotExist("//div[@id='translate_bar']");
        $this->clickAndWait("//a[contains(@href,'lang=en')]");
        $this->mustNotExist("//div[@id='translate_bar']");
        $this->open($this->getAttribute("//a[contains(@href,'/tr/page')]@href"));
        //$this->clickAndWait("//a[contains(text(),'aslkfdalkewjfalkwejfa')]");
        $this->clickAndWait("//a[contains(@href,'/base_lang') and contains(text(),'unknown')]");
        $this->select("//select[@name='base_lang']", "Kiswahili");
        $this->submitForm();
        $this->mouseOver("//a[contains(@href,'/base_lang') and contains(text(),'Swahili')]");
        $this->open($unknown_url);
        $this->assertContains("Parts of this page are in Swahili", 
            $this->getText("//div[@id='translate_bar']"));        
            
        $this->logout();
    }    
    
    function _testTranslateInterface()
    {        
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
        $this->type("//input[@name='password']", 'daspdofaa');
        $this->type("//input[@name='password2']", 'DASPDOFAA');
        $this->type("//input[@name='email']", 'nobody@nowhere.com');
        $this->type("//input[@name='phone']", '555-1212');
        $this->submitForm();
        $this->ensureBadMessage();
        
        $this->type("//input[@name='password2']", 'daspdofaa');
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
        $this->mouseOver("//td//a[contains(text(),'$value')]");
        $this->mustNotExist("//td//a[contains(text(),'$value2')]");
        
        $this->clickAndWait("//a[contains(@href,'anonymous')]");
        
        $this->clickAndWait("//tr[.//td[contains(text(),'$value2')]]//a[contains(@href,'delta=1')]");
        $this->waitForElement("//strong[contains(text(),'0')]");

        $this->clickAndWait("//h2//a[contains(@href,'/module/comment')]");
        $this->mouseOver("//td//a[contains(text(),'$value2')]");
        $this->mustNotExist("//td//a[contains(text(),'$value')]");        
        
        // test filtering
        $this->select("//select[@name='status']", "Translated");
        $this->waitForElement("//select[@name='status']//option[@value='translated' and @selected='selected']");
        $this->waitForElement("//a[contains(@href,'anonymous')]");
        $this->mustNotExist("//a[contains(@href,'name_5Fsaid')]");

        $this->select("//select[@name='status']", "Not translated");
        $this->waitForElement("//select[@name='status']//option[@value='empty' and @selected='selected']");
        $this->mustNotExist("//a[contains(@href,'anonymous')]");
        $this->waitForElement("//a[contains(@href,'name_5Fsaid')]");        
        
        $this->type("//input[@name='q']","deleted");
        $this->waitForElement("//input[@name='q' and @value='deleted']");
        
        $this->mustNotExist("//a[contains(@href,'anonymous')]");
        $this->mustNotExist("//a[contains(@href,'name_5Fsaid')]");        
        $this->clickAndWait("//a[contains(@href,'deleted')]");
        
        // test filters persist through navigation (assumes only 2 keys in comment module containing 'deleted')
        $this->waitForElement("//a[contains(@href,'/next')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->clickAndWait("//a[contains(@href,'/next')]");
        $this->mustNotExist("//a[contains(@href,'/next')]");
        
        $this->assertEquals("deleted", $this->getValue("//input[@name='q']"));
        $this->mustNotExist("//a[contains(@href,'anonymous')]");
        $this->mustNotExist("//a[contains(@href,'name_5Fsaid')]");                
        $this->mouseOver("//a[contains(@href,'deleted')]");
        
        $this->type("//input[@name='q']","");
        $this->waitForElement("//input[@name='q' and @value='']");
        $this->select("//select[@name='status']", "All");        
        
        $this->waitForElement("//a[contains(@href,'anonymous')]");
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
                $this->click("//div[@class='admin_links']//a[contains(@href,'delete')]");
            }
            catch (Exception $ex)
            {
                return;
            }
            
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
        }
    }
    
    function _testDetectLanguage($url, $language)
    {
        $this->open($url);
        $this->assertContains("Parts of this page are in $language", $this->getText("//div[@id='translate_bar']"));    
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