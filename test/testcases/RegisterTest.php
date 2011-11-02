<?php

class RegisterTest extends SeleniumTest
{
    public $name;
    public $username;
    public $email;
    public $username2;
    public $email2;
    public $name2;

    public function test()
    {        
        $this->setTimestamp(time());
    
        $this->_testRegister();
        $this->_testResetPassword();
        $this->_testSettings();
        $this->_testTranslate();
        $this->_testPost();
        $this->_testEditPages();
        $this->_testAddPage();
        $this->_testEditContact();
        $this->_testEditHome();
        $this->_testMakePublic();
        $this->_testAnotherRegister();
        $this->_testMessages();
        $this->_testDeleteOrg();
    }
    
    private function _testResetPassword()
    {
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->clickAndWait("//a[contains(@href,'pg/forgot_password')]");
        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();

        $email = $this->getLastEmail("Request for new password");

        $url = $this->getLinkFromText($email);

        $this->setUrl($url);

        $this->type("//input[@name='password']", "abcdefg");
        $this->type("//input[@name='password2']", "abcdefgh");
        $this->submitForm();
        
        $this->ensureBadMessage();
        
        $this->type("//input[@name='password']", "abcdefgh");
        $this->submitForm();

        $this->ensureGoodMessage();
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        
        $this->setUrl($url); // password code can only be used once        
        $this->ensureBadMessage();        

        $this->clickAndWait("//a[contains(@href, 'home')]");
        
        // test reset password by sms
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->clickAndWait("//a[contains(@href,'pg/forgot_password')]");
        
        $this->type("//input[@name='username']", "16505550001");
        $this->submitForm();                
        
        $this->type("//input[@name='c']", "XXX");
        $this->submitForm();
        $this->ensureBadMessage();
        
        $sms = $this->getLastSMS("new password");
        
        if (!preg_match('#Message\:\s+(?P<code>\w+)#', $sms, $match))
        {
            throw new Exception("expected message to start with password reset code");
        }
        
        $code = $match['code'];
        
        $this->type("//input[@name='c']", $code);
        $this->submitForm();
        
        $this->type("//input[@name='password']", "abcdefgh");
        $this->type("//input[@name='password2']", "abcdefgh");
        $this->submitForm();
        $this->ensureGoodMessage();
        
        $this->clickAndWait("//a[contains(@href, 'home')]");
    }

    private function _testRegister()
    {
        $this->open("/");

        $this->clickAndWait("//a[contains(@href,'/org/new')]");

        /* qualification */
        $this->click("//input[@name='org_type' and @value='p']");
        $this->submitForm();
        $this->click("//input[@name='org_type' and @value='np']");
        $this->submitForm();
        $this->ensureBadMessage();
        $this->click("//input[@name='country' and @value='other']");
        $this->submitForm();
        sleep(2);
        $this->ensureBadMessage();
        $this->click("//input[@name='country' and @value='tz']");
        $this->submitForm();

        /* create account */
        
        $this->name = "testorg".time();

        $this->ensureGoodMessage();

        $this->type("//input[@name='org_name']", $this->name);
        $this->type("//input[@name='city']", "Wete");
        $this->select("//select[@name='region']", "Pemba North");
        $this->type("//input[@name='username']", "t<b>x</b>");
        $this->type("//input[@name='password']", "password");
        $this->type("//input[@name='password2']", "password2");        
        
        $this->email = "nobody+".time()."@nowhere.com";
        
        $this->type("//input[@name='email']", $this->email);
        
        $this->type("//input[@name='phone']", "16505550001");
        
        $this->submitForm();
        sleep(2);
        $this->ensureBadMessage();
        
        $this->username = "selenium".time();

        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
        sleep(2);
        $this->ensureBadMessage();

        $this->type("//input[@name='password2']", "password");
        $this->type("//input[@name='username']", "org");
        $this->submitForm();
        $this->ensureBadMessage();

        $this->type("//input[@name='username']", "testposter3");
        $this->submitForm();
        
        $this->ensureBadMessage("too easy to guess");
        
        $this->type("//input[@name='password']", "tanzania1");
        $this->type("//input[@name='password2']", "tanzania1");        
        $this->submitForm();
        
        $this->ensureBadMessage("too easy to guess");
        
        $this->type("//input[@name='password']", "testorg");
        $this->type("//input[@name='password2']", "testorg");
        $this->submitForm();
        
        $this->ensureBadMessage("too easy to guess");
        
        $this->type("//input[@name='password']", "xadlfkj");
        $this->type("//input[@name='password2']", "xadlfkj");
        $this->submitForm();
        
        // duplicate username: suggested to log in instead of create account
        $this->mouseOver("//a[contains(@href,'/pg/login?username=testposter3')]");
        $this->submitForm();
        
        // can't create new account, need to choose new username
        $this->ensureBadMessage();
        
        $this->name = "Test Org";
        
        $this->type("//input[@name='org_name']", $this->name);                
        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
                
        // warn for possible duplicate name, but can still create new account
                
        $this->mouseOver("//a[contains(@href,'/pg/login?username=testorg')]");
        $this->submitForm();
        
        sleep(2);

        /* set up homepage */
        $this->ensureGoodMessage();

        $this->typeInFrame("//iframe", "testing the website");
        $this->check("//input[@name='sector[]' and @value='3']");
        $this->check("//input[@name='sector[]' and @value='99']");
        $this->type("//input[@name='sector_other']", "another sector");
        $this->click("//a[@id='theme_brick']");

        $this->submitForm();

        /* home page */

        $this->ensureGoodMessage();

        $this->mouseOver("//h2//a[text()='{$this->name}']");
        $this->mouseOver("//h3[text()='Wete, Tanzania']");
        $this->mouseOver("//a[contains(@href,'pg/browse?list=1&sector=3') and text()='Conflict resolution']");
    }

    private function _testPost()
    {
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
        $this->typeInFrame("//iframe", "this is a test post");
        $this->submitForm();
        $this->mouseOver("//p[contains(text(), 'this is a test post')]");
        
        $this->clickAndWait("//a[contains(@href,'pg/feed')]");
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), 'this is a test post')]");
        $this->clickAndWait("//a[contains(text(), 'News update')]");
        $this->mouseOver("//p[contains(text(), 'this is a test post')]");               
    }
    
    private function _testEditContact()
    {
        $this->clickAndWait("//div[@id='site_menu']//a[contains(@href,'contact')]");
        $this->mouseOver("//a[@href='mailto:{$this->email}']");
        $this->clickAndWait("//a[contains(@href,'contact/edit')]");
        $this->uncheck("//input[@name='public_email[]']");
        $this->type("//input[@name='phone_number']", "1234567");
        $this->type("//input[@name='contact_name']", "Test Person");

        $this->clickAndWait("//button[@type='submit']");

        $this->mouseOver("//td[contains(text(),'1234567')]");
        $this->mouseOver("//td[contains(text(),'Test Person')]");
        $this->mustNotExist("//a[@href='mailto:{$this->email}']");

        $this->clickAndWait("//a[contains(@href,'contact/edit')]");
        $this->clickAndWait("//button[@id='widget_delete']");
        $this->getConfirmation();
        $this->ensureGoodMessage();
        $this->open("/{$this->username}");
        $this->mouseOver("//a[contains(@href,'contact')]"); // todo items
        $this->mustNotExist("//div[@id='site_menu']//a[contains(@href,'contact')]");
    }

    private function _testTranslate()
    {
        $this->clickAndWait("//a[contains(@href,'{$this->username}')]");
        $this->clickAndWait("//a[contains(@href,'lang=sw')]");
        $this->mouseOver("//div[@class='section_header' and contains(text(),'Lengo')]");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'website')]");
        $this->clickAndWait("//a[contains(@href,'trans=3')]");
        $this->mustNotExist("//div[contains(@class, 'section_content')]//p[contains(text(),'website')]");
        $this->waitForElement("//div[contains(@class, 'section_content')]//p[contains(text(),'tovuti')]");
        $this->clickAndWait("//a[contains(@href,'lang=en')]");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'website')]");
    }

    private function _testEditHome()
    {
        $this->clickAndWait("//a[contains(@href,'{$this->username}/home')]");
    
        $this->assertContains("Mission", $this->getText("//div[@class='section_header']"));
        $this->assertContains("Latest Updates", $this->getText("//div[@class='section_header'][2]"));
        $this->assertContains("Sectors", $this->getText("//div[@class='section_header'][3]"));
        $this->assertContains("Location", $this->getText("//div[@class='section_header'][4]"));
    
        // update mission
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->clickAndWait("//a[contains(text(),'Mission')]");
        $this->typeInFrame("//iframe", "new mission!");
        $this->clickAndWait("//button[@type='submit']");
        $this->mouseOver("//div[contains(@class, 'section_content')]//p[contains(text(),'new mission!')]");
        $this->mouseOver("//div[@id='site_menu']//a[@class='selected']");
        
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        
        // reorder sections
        
        $locationRowId = $this->getAttribute("//tr[.//a[contains(text(),'Location')]]@id");
        $sectorsRowId = $this->getAttribute("//tr[.//a[contains(text(),'Sectors')]]@id");
        $updatesRowId = $this->getAttribute("//tr[.//a[contains(text(),'Latest Updates')]]@id");
        $missionRowId = $this->getAttribute("//tr[.//a[contains(text(),'Mission')]]@id");
        
        $this->mouseOver("//tr[4]//a[@id='{$locationRowId}_up']");
        
        $this->click("//a[@id='{$locationRowId}_up']");
        
        $this->retry('mouseOver', array("//tr[1]//a[@id='{$missionRowId}_up']"));
        $this->retry('mouseOver', array("//tr[2]//a[@id='{$updatesRowId}_up']"));        
        $this->retry('mouseOver', array("//tr[3]//a[@id='{$locationRowId}_up']"));
        $this->retry('mouseOver', array("//tr[4]//a[@id='{$sectorsRowId}_up']"));
        
        $this->click("//a[@id='{$locationRowId}_up']");
        
        $this->retry('mouseOver', array("//tr[1]//a[@id='{$missionRowId}_up']"));
        $this->retry('mouseOver', array("//tr[2]//a[@id='{$locationRowId}_up']"));        
        $this->retry('mouseOver', array("//tr[3]//a[@id='{$updatesRowId}_up']"));
        $this->retry('mouseOver', array("//tr[4]//a[@id='{$sectorsRowId}_up']"));        
        
        $this->click("//a[@id='{$locationRowId}_up']");

        $this->retry('mouseOver', array("//tr[1]//a[@id='{$locationRowId}_up']"));
        $this->retry('mouseOver', array("//tr[2]//a[@id='{$missionRowId}_up']"));        
        $this->retry('mouseOver', array("//tr[3]//a[@id='{$updatesRowId}_up']"));
        $this->retry('mouseOver', array("//tr[4]//a[@id='{$sectorsRowId}_up']"));                        
        
        $this->click("//a[@id='{$sectorsRowId}_up']");

        $this->retry('mouseOver', array("//tr[1]//a[@id='{$locationRowId}_up']"));
        $this->retry('mouseOver', array("//tr[2]//a[@id='{$missionRowId}_up']"));        
        $this->retry('mouseOver', array("//tr[3]//a[@id='{$sectorsRowId}_up']"));
        $this->retry('mouseOver', array("//tr[4]//a[@id='{$updatesRowId}_up']"));                        
        
        // create custom section
        $this->clickAndWait("//a[contains(@href,'/add')]");
        $this->type("//input[@name='title']",'My New Section');
        $this->typeInFrame("//iframe", "yay!");
        
        $this->submitForm();

        $this->assertContains("Location", $this->getText("//div[@class='section_header']"));
        $this->assertContains("Mission", $this->getText("//div[@class='section_header'][2]"));
        $this->assertContains("Sectors", $this->getText("//div[@class='section_header'][3]"));
        $this->assertContains("Latest Updates", $this->getText("//div[@class='section_header'][4]"));        
        $this->assertContains("My New Section", $this->getText("//div[@class='section_header'][5]"));        
        $this->mouseOver("//div[contains(@class, 'section_content')][5]//p[contains(text(),'yay!')]");        

        // edit custom section
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        
        $this->clickAndWait("//a[contains(text(),'My New Section')]");

        $this->type("//input[@name='title']",'New Section 2');
        $this->typeInFrame("//iframe", "yay 2!");
        $this->submitForm();
        
        $this->assertContains("New Section 2", $this->getText("//div[@class='section_header'][5]"));        
        $this->mouseOver("//div[contains(@class, 'section_content')][5]//p[contains(text(),'yay 2!')]");
        
        // delete sectors section
        $this->mouseOver("//a[text()='Conflict resolution']");
        $this->mustNotExist("//a[text()='Tourism']");                
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->clickAndWait("//a[contains(text(),'Sectors')]");
        $this->submitForm("//button[@id='widget_delete']");        
        $this->getConfirmation();
        $this->mustNotExist("//div[@class='section_header' and contains(text(),'Sectors')]");
        
        // bring sectors section back
        $this->clickAndWait("//div[@class='widget_list']//a[contains(text(),'Sectors')]");
        $this->check("//input[@name='sector[]' and @value='21']");
        $this->submitForm();
        $this->mouseOver("//div[@class='section_header' and contains(text(),'Sectors')]");
        $this->mouseOver("//a[text()='Conflict resolution']");
        $this->mouseOver("//a[text()='Tourism']");
        
        // test delete feed item from news update
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), 'this is a test post')]");
        $this->mouseOver("//div[contains(@class,'feed_post')]//a[contains(@href,'projects')]");
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->clickAndWait("//a[contains(text(),'Latest Updates')]");

        $this->clickAndWait("//div[contains(@class,'feed_post') and .//div[@class='feed_snippet' and contains(text(), 'this is a test post')]]//a[@class='admin_links']");        
        $this->getConfirmation();
        $this->ensureGoodMessage();
        $this->mustNotExist("//div[@class='feed_snippet' and contains(text(), 'this is a test post')]");        
        $this->mouseOver("//div[contains(@class,'feed_post')]//a[contains(@href,'projects')]");
        $this->submitForm();
        $this->mustNotExist("//div[@class='feed_snippet' and contains(text(), 'this is a test post')]");        
        $this->mouseOver("//div[contains(@class,'feed_post')]//a[contains(@href,'projects')]");

        // test edit location
        $this->mouseOver("//em[contains(text(),'Wete, Pemba North, Tanzania')]");        
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->clickAndWait("//a[contains(text(),'Location')]");
        
        $this->assertEquals("Wete", $this->getValue("//input[@name='city']"));
        $this->type("//input[@name='city']", "Vitongoji");
        $this->submitForm();
        $this->mouseOver("//em[contains(text(),'Vitongoji, Pemba North, Tanzania')]");        
        $this->assertContains("Wete, Tanzania", $this->getText("//h3")); // tagline is not updated
    }

    private function _testEditPages()
    {
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
                
        $this->clickAndWait("//a[contains(@href,'home/edit')]");
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->clickAndWait("//a[contains(@href,'projects/edit')]");
        $this->typeInFrame("//iframe", "we test stuff");
        $this->clickAndWait("//button[@type='submit']");
        sleep(2);
        $this->mouseOver("//div[contains(@class,'section_content')]//p[contains(text(), 'we test stuff')]");
        
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
        
        // test reordering pages so News is first
        $homeRowId = $this->getAttribute("//tr[.//a[contains(text(),'Home')]]@id");
        $newsRowId = $this->getAttribute("//tr[.//a[contains(text(),'News')]]@id");
        $projectsRowId = $this->getAttribute("//tr[.//a[contains(text(),'Projects')]]@id");
        
        $this->mouseOver("//tr[2]//a[@id='{$newsRowId}_up']");
        
        $this->click("//a[@id='{$newsRowId}_up']");
        
        $this->retry('mouseOver', array("//tr[1]//a[@id='{$newsRowId}_up']"));
        $this->retry('mouseOver', array("//tr[2]//a[@id='{$homeRowId}_up']"));        
        $this->retry('mouseOver', array("//tr[3]//a[@id='{$projectsRowId}_up']"));
        
        $this->clickAndWait("//a[@title='Your home page']");
        
        // test that default page is the first one in the menu
        $this->assertEquals("New Name", $this->getTitle());
        $this->assertContains("Wete, Tanzania", $this->getText("//h3"));
        $this->mouseOver("//a[@class='selected']//span[contains(text(),'News')]");
        
        // test that "Home" page is now the second one in the menu
        $this->clickAndWait("//div[@id='site_menu']//a[2]");        
        $this->assertContains("Tanzania", $this->getText("//h3"));

    }
    
    private function _testAddPage()
    {
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
        $this->clickAndWait("//a[contains(@href,'/add_page')]");
        
        $this->type("//input[@name='title']", "New page");
        $this->type("//input[@name='widget_name']", "settings!!!");
        $this->typeInFrame("//iframe", "another page!!!!!!");
        $this->submitForm();
                
        $this->ensureBadMessage(); // invalid characters in widget_name
        $this->type("//input[@name='widget_name']", "settings");
        $this->clickAndWait("//button[@type='submit']");
        
        $this->assertContains("New page", $this->getTitle());
        $this->mouseOver("//a[@class='selected' and contains(@href,'page/settings')]");
        $this->mouseOver("//div[contains(@class,'section_content')]//p[contains(text(), 'another page!!!!')]");
        
        // change title
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->type("//input[@name='title']", "New title");
        
        $this->clickAndWait("//button[@type='submit']");
              
        $this->assertContains("New title", $this->getTitle());
        $this->mouseOver("//a[@class='selected' and contains(@href,'page/settings')]");
        $this->mouseOver("//div[contains(@class,'section_content')]//p[contains(text(), 'another page!!!!')]");        
        
        // change page URL
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->type("//input[@name='widget_name']", "home");
        $this->submitForm();
        $this->ensureBadMessage(); // duplicate page name
        $this->type("//input[@name='widget_name']", "new-url");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//a[@class='selected' and contains(@href,'page/new-url')]");
        $this->mustNotExist("//a[contains(@href,'page/settings')]");
        $this->mouseOver("//div[contains(@class,'section_content')]//p[contains(text(), 'another page!!!!')]");        

        // test not-found redirects
        $this->open("/{$this->username}/page/settings");
        $this->mouseOver("//a[@class='selected' and contains(@href,'page/new-url')]");
        $this->mustNotExist("//a[contains(@href,'page/settings')]");
        
        // change URL again, test redirect chain
        $this->clickAndWait("//div[@id='edit_submenu']//a");
        $this->type("//input[@name='widget_name']", "anotherurl");        
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//a[@class='selected' and contains(@href,'page/anotherurl')]");
        $this->open("/{$this->username}/page/settings");
        $this->assertContains("New title", $this->getTitle());
        $this->mouseOver("//a[@class='selected' and contains(@href,'page/anotherurl')]");        

        // test non-matching url is not redirected
        $this->open("/{$this->username}/page/new-url2");
        
        $self = $this;
        retry(function() use ($self) {
            $self->assertContains("/{$self->username}/page/new-url2", $self->getLocation());
        });
        $this->assertContains('Page not found', $this->getTitle());        
        
        // test redirects are only for the specified user
        $this->open("/testorg/page/settings");
        retry(function() use ($self) {
            $self->assertContains('/testorg/page/settings', $self->getLocation());
        });
        
        $this->assertContains('Page not found', $this->getTitle());
        
        // make sure page names don't conflict with built-in actions
        $this->clickAndWait("//a[@id='usersettings']");
        $this->type("//input[@name='phone']", "123456890");
        $this->submitForm();
        
        $this->ensureGoodMessage();
        
        $this->clickAndWait("//a[@id='usersettings']");
        $this->assertEquals("123456890", $this->getValue("//input[@name='phone']"));        
        
        $this->clickAndWait("//a[@title='Your home page']");
    }

    private function _testSettings()
    {
        // change name
        $this->clickAndWait("//a[contains(@href,'/settings')]");
        $this->type("//input[@name='name']", "New Name");
        $this->submitForm();
        $this->ensureGoodMessage();
        
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        $this->mouseOver("//h2//a[text()='New Name']");
        
        $this->setTimestamp(time() + 600); // force entering old password
        
        // change password        
        $this->clickAndWait("//a[contains(@href,'/settings')]");
        $this->clickAndWait("//a[contains(@href,'/password')]");
        
        $this->type("//input[@name='old_password']", "abcdefgh");
        $this->type("//input[@name='password']", "asdfasdf2");
        $this->type("//input[@name='password2']", "asdfasdf3");        
        $this->submitForm();        
        $this->ensureBadMessage('did not match');
        
        $this->type("//input[@name='old_password']", "password");
        $this->type("//input[@name='password']", "asdfasdf2");
        $this->type("//input[@name='password2']", "asdfasdf2");
        $this->submitForm();        
        $this->ensureBadMessage('password was incorrect');
        
        $this->type("//input[@name='old_password']", "abcdefgh");
        $this->type("//input[@name='password']", "wete13");
        $this->type("//input[@name='password2']", "wete13");        
        $this->submitForm();
        $this->ensureBadMessage('too easy');

        $this->type("//input[@name='password']", "dglkjwr");
        $this->type("//input[@name='password2']", "dglkjwr");        
        $this->submitForm();
        
        // test new password works for login
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->login($this->username, 'password');
        $this->ensureBadMessage();        
        $this->login($this->username, 'abcdefgh');
        $this->ensureBadMessage();        
        $this->login($this->username, 'dglkjwr');
        $this->ensureGoodMessage();        
                       
        // change username        
        $this->clickAndWait("//a[contains(@href,'/settings')]");
        
        // don't need to enter old password just after logging in
        $this->clickAndWait("//a[contains(@href,'/password')]");
        $this->mouseOver("//input[@name='password']");
        $this->mustNotExist("//input[@name='old_password']");
        
        $this->setTimestamp(time()); 

        $this->clickAndWait("//a[contains(@href,'/settings')]");        
        
        $this->clickAndWait("//a[contains(@href,'/username')]");
        
        $this->type("//input[@name='username']", "testorg");
        $this->submitForm();
        $this->ensureBadMessage(); // duplicate username
        
        $this->type("//input[@name='username']", "mod");
        $this->submitForm();
        $this->ensureBadMessage(); // invalid username

        $this->type("//input[@name='username']", "x");
        $this->submitForm();
        $this->ensureBadMessage(); // too short username

        $oldUsername = $this->username;
        $this->username = "selenium".time();
        $this->type("//input[@name='username']", $this->username);
        $this->submitForm();
        $this->ensureGoodMessage(); 
        
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
        $this->mouseOver("//h2//a[text()='New Name']");
        
        // test new username works for login
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/login')]");
        $this->login($oldUsername, 'dglkjwr');
        $this->ensureBadMessage();
        $this->login($this->username, 'dglkjwr');
        $this->ensureGoodMessage();        
    }

    private function _testMakePublic()
    {
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->clickAndWait("//a[contains(@href,'pg/feed')]");
        $this->mustNotExist("//a[contains(@href, '/{$this->username}')]");
        $this->clickAndWait("//a[contains(@href,'pg/search')]");
        $this->type("//input[@name='q']", $this->username);
        $this->submitForm();
        $this->mouseOver("//div[@class='padded' and contains(text(),'No results')]");

        $this->open("/{$this->username}");
        sleep(2);
        $this->ensureBadMessage();
        $this->mustNotExist("//h2");

        $this->login('testadmin','secretpassw0rd');

        $this->clickAndWait("//a[contains(@href, 'approval=1')]");
        $this->getConfirmation();
        $this->ensureGoodMessage();

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");

        $this->clickAndWait("//a[contains(@href,'pg/search')]");
        $this->type("//input[@name='q']", $this->username);
        $this->submitForm();
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");

        $this->mouseOver("//h2//a[text()='New Name']");
        $this->mustNotExist("//a[contains(@href,'home/edit')]");

        $this->clickAndWait("//a[contains(@href,'pg/feed')]");
        $this->clickAndWait("//a[contains(@href, '/{$this->username}')]");
    }

    private function _testAnotherRegister()
    {
        $this->clickAndWait("//a[contains(@href,'home')]");
        $this->clickAndWait("//a[contains(@href,'/org/new')]");

        /* qualification */
        $this->click("//input[@name='org_type' and @value='np']");
        $this->click("//input[@name='country' and @value='tz']");
        $this->submitForm();

        /* create account */

        $this->ensureGoodMessage();

        $this->username2 = "selenium_-_".time();
        $this->name2 = "Test Partner ".time();
        $this->email2 = "nobody+".time()."@nowhere.com";

        $this->type("//input[@name='org_name']", $this->name2);
        $this->type("//input[@name='city']", "Konde");
        $this->select("//select[@name='region']", "Pemba North");        
        $this->type("//input[@name='username']", $this->username2);
        $this->type("//input[@name='password']", "aksldflksj");
        $this->type("//input[@name='password2']", "aksldflksj");
        $this->type("//input[@name='email']", $this->email2);
        $this->submitForm();

        /* set up homepage */

        $this->ensureGoodMessage();

        $this->typeInFrame("//iframe", "being a partner");
        $this->check("//input[@name='sector[]' and @value='4']");
        $this->check("//input[@name='sector[]' and @value='99']");
        $this->click("//a[@id='theme_wovengrass']");

        $this->submitForm();

        /* home page */

        $this->ensureGoodMessage();

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->retry('mouseOver', array("//a[@id='loginButton']"));

        $email = $this->getLastEmail("New organization registered: Test Partner");
        $url = $this->getLinkFromText($email);
        $this->open($url);
        
        $this->retry('mouseOver', array("//input[@name='username']"));

        $this->login('testadmin','secretpassw0rd');
        $this->ensureGoodMessage();
        $this->open("/{$this->username2}");
        $this->clickAndWait("//a[contains(@href, 'approval=1')]");
        $this->getConfirmation();
        $this->ensureGoodMessage();

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        
        $this->open('/pg/login');
        $this->login($this->username, 'dglkjwr');
        $this->ensureGoodMessage();
    }

    private function _testMessages()
    {
        $this->open("/{$this->username2}");
        $this->clickAndWait("//a[contains(@href,'/send_message')]");
        $this->type("//input[@name='subject']","Test Subject");
        $this->type("//textarea[@name='message']", "Test Message");
        $this->submitForm();
        $this->ensureGoodMessage();
        $email = $this->getLastEmail("Test Subject");

        $this->assertContains("Test Message",$email);
        $this->assertContains("To: {$this->name2} <{$this->email2}>", $email);
        $this->assertContains("Reply-To: New Name <{$this->email}>", $email);
    }

    private function _testDeleteOrg()
    {
        $this->open("/admin/entities");
        $this->login('testadmin','secretpassw0rd');
        
        $this->open("/{$this->username}");

        try
        {
            $this->click("//a[contains(@href,'approval=0')]");
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
        }
        catch (Testing_Selenium_Exception $ex) {}

        try
        {
            $this->click("//a[contains(@href,'approval=-1')]");
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
        }
        catch (Testing_Selenium_Exception $ex) {}

        $this->click("//a[contains(@href,'disable')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);            

        $this->ensureGoodMessage();
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
    }
}
