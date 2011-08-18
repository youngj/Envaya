<?php

class NetworkTest extends SeleniumTest
{
    private $email14;
    
    public function test()
    {           
        // delete existing network from previous tests
        $this->clearNetworks();
        
        $this->open('/testposter14/contact');
        $this->email14 = $this->getText("//table[@class='contactTable']//a");
        
        // disable network notifications for testposter13
        $this->open('/pg/login');
        $this->login('testposter13','testtest');
        $this->ensureGoodMessage();
        $this->clickAndWait("//a[contains(@href,'settings')]");
        $this->uncheck("//input[@name='notifications[]' and @value='4']");
        $this->submitForm();        
        
        // log in as testorg
        $this->open('/pg/login');
        $this->login('testorg','testtest');
        $this->ensureGoodMessage();
                
        // click todo item
        $this->clickAndWait("//ul[@class='todo_steps']//a[contains(@href,'page/network/edit')]");
        
        // add org by name    
        $this->clickAddRelationship();
        $this->type("//input[@name='name']","Test Poster12");
        $this->type("//input[@name='phone_number']","i dunno");
        $this->click("//button");
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testposter12')]"));
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(@href, 'testposter12')]");
        $email = $this->getLastEmail("testposter12/page/network/edit");
        $this->assertContains("testorg/network",$email);
        
        // add description
        $this->clickAndWait("//a[contains(@href, 'edit_relationship')]");
        $this->typeInFrame("//iframe", "yay partners");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->assertContains("yay partners", $this->getText("//div[@class='feed_snippet']"));
                                
        // add org by phone number (test canonicalizing)
        $this->clickAddRelationship();
        $this->type("//input[@name='name']","a;lskdjfalkdsfj");
        $this->type("//input[@name='phone_number']","+25501313131313,123124904");
        $this->click("//button");
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testposter13')]"));
        $this->mustNotExist("//span[@class='search_url' and contains(text(), 'testposter14')]");
        $this->mustNotExist("//span[@class='search_url' and contains(text(), 'testposter12')]");
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(@href, 'testposter13')]");
        sleep(2); 
        
        // should not send email because network notifications for this user are disabled
        $email = $this->getLastEmail("testorg/network");
        $this->assertNotContains("testposter13",$email);
        $this->assertContains("testposter12",$email);
                
        // add org by email
        $this->clickAddRelationship();
        $this->type("//input[@name='name']","a;lskdjfalkdsfj");
        $this->type("//input[@name='phone_number']","12412415436,123124904");
        $this->type("//input[@name='email']",$this->email14);
        $this->click("//button");        
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testposter14')]"));
        $this->mustNotExist("//span[@class='search_url' and contains(text(), 'testposter13')]");
        $this->mustNotExist("//span[@class='search_url' and contains(text(), 'testposter15')]");
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(@href, 'testposter14')]");               
        $email = $this->getLastEmail("testposter14/page/network/edit");
        $this->assertContains("testorg/network",$email);        
                        
        // add org by website (missing http://)
        $this->clickAddRelationship();
        $this->type("//input[@name='name']","a;lskdjfalkdsfj");
        $this->type("//input[@name='phone_number']","12412415436,123124904");
        $this->type("//input[@name='website']","localhost/testposter15");
        $this->click("//button");        
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testposter15')]"));
        $this->mustNotExist("//span[@class='search_url' and contains(text(), 'testposter14')]");
        $this->mustNotExist("//span[@class='search_url' and contains(text(), 'testposter16')]");
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(@href, 'testposter15')]");          
        $email = $this->getLastEmail("testposter15/page/network/edit");
        $this->assertContains("testorg/network",$email);        
                
        // can't add duplicate org
        $this->clickAddRelationship();
        $this->type("//input[@name='name']","a;lskdjfalkdsfj");
        $this->type("//input[@name='phone_number']","1515151515");
        $this->click("//button");        
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testposter15')]"));
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureBadMessage();        
        
        // can't add self
        $this->type("//input[@name='name']","a;lskdjfalkdsfj");
        $this->type("//input[@name='phone_number']","");
        $this->type("//input[@name='website']","localhost/testorg");
        
        $this->assertEquals('true', $this->getEval('window.dirty'));
        
        $this->click("//button");        
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testorg')]"));
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureBadMessage();            
        
        // dirtiness persists after form submit failure
        $this->assertEquals('true', $this->getEval('window.dirty'));
        $this->getEval('window.setDirty(false);'); // avoid onbeforesubmit warning, selenium can't deal with it
        
        // add org by clicking link at top of their site
        $this->open("/testposter16");
        $this->clickAddRelationship();
        $this->mustNotExist("//input[@name='name']");
        $this->typeInFrame("//iframe", "hooray partners");
        $this->submitForm();
        $this->mouseOver("//a[contains(@href, 'testposter16')]");          
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(),'hooray partners')]");
        $notify_email = $this->getLastEmail("testposter16/page/network/edit");
        $this->assertContains("testorg/network", $notify_email);        
                
        // add new org, no search results        
        $this->clickAddRelationship();
        $orgName1 = "asldkfjalskdjfalkejfaef";
        $this->type("//input[@name='name']",$orgName1);
        $this->click("//button");        
        $this->retry('mouseOver', array("//div[@class='modalButtons']//input[@type='submit']"));
        $this->mustNotExist("//div[@class='modalBox']//input[@type='checkbox']");
        $this->clickAndWait("//div[@class='modalButtons']//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//div[@class='search_listing']//b[contains(text(), '$orgName1')]");          
        
        // add new org, with search results, no invitation
        $this->clickAddRelationship();
        $orgName2 = "alkdsfjalkjflakefjakewf";
        $this->type("//input[@name='name']", $orgName2);
        $this->type("//input[@name='email']",$this->email14);
        $this->type("//input[@name='website']","www.google.com");
        $this->click("//button");  
        $this->retry('mouseOver', array("//a[@class='selectMemberNone']"));
        $this->click("//a[@class='selectMemberNone']");                        
        $this->mustNotExist("//div[@class='modalBox']//input[@type='checkbox']"); // can't invite email that is already registered
        $this->click("//div[@class='modalClose']");
        $this->type("//input[@name='email']", "nobody@nowhere.com");
        $this->click("//button");          
        $this->retry('uncheck', array("//div[@class='modalBox']//input[@type='checkbox']"));        
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(text(), '$orgName2') and contains(@href,'http://www.google.com')]");          
        $this->mouseOver("//a[@href='mailto:nobody@nowhere.com']");                  
        sleep(2);
        $email = $this->getLastEmail("testorg/network");        
        $this->assertNotContains($orgName2, $email);           
        $this->assertNotContains('nobody@nowhere.com', $email);           
        $this->assertContains('testposter16', $email); // last email should still be previous notification
        
        // add new org, invite to join        
        $this->clickAddRelationship();
        $invitedOrgName = "lkajsd;lkajfdlkajdsflka";
        $invitedOrgEmail = "foo+q".time()."@nowhere.com";
        $this->type("//input[@name='name']", $invitedOrgName);
        $this->type("//input[@name='email']", $invitedOrgEmail);
        $this->click("//button");  
        $this->retry('mouseOver', array("//div[@class='modalBox']//input[@type='checkbox']"));        
        $this->click("//div[@class='modalClose']");      // make sure we can close the modal box...
        $this->mustNotExist("//div[@class='modalButtons']//input[@type='submit']");                
        $this->click("//button");                       // ...and open it again
        $this->clickAndWait("//div[@class='modalButtons']//input[@type='submit']");        
        $this->ensureGoodMessage();
        $inviteEmail = $this->getLastEmail($invitedOrgEmail);        
        $this->mouseOver("//a[@href='mailto:$invitedOrgEmail']");          

        // make sure we can't invite the same email address twice in a row
        $this->clickAddRelationship();
        $notInvitedOrgName = "laewflkaewjflkawejflkaewjf";
        $this->type("//input[@name='name']", $notInvitedOrgName);
        $this->type("//input[@name='email']", $invitedOrgEmail);
        $this->click("//button");  
        $this->retry('mouseOver', array("//div[@class='modalButtons']//input[@type='submit']"));        
        $this->mustNotExist("//div[@class='modalBox']//input[@type='checkbox']");
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        sleep(2);
        $this->assertNotContains($notInvitedOrgName, $this->getLastEmail($invitedOrgEmail));        
        $this->click("//div[@class='search_listing' and .//b[contains(text(), '$notInvitedOrgName')]]//a[contains(@href,'delete_relationship')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        
        // click email link to add reverse relationship
        $this->open('/testposter16/network');
        sleep(1);
        $this->mustNotExist("//a[contains(@href, 'testorg')]"); // suggested reverse relationship should not be public
        
        $approveUrl = $this->getLinkFromText($notify_email, 1);
        $this->setUrl($approveUrl);
        $this->login('testposter16','testtest');
        $this->ensureGoodMessage();
        $this->typeInFrame("//iframe", "whee reverse relationship");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(@href, 'testorg')]");          
        $this->assertContains("whee reverse relationship", $this->getText("//div[@class='feed_snippet']"));
        
        // verify notification email sent out to testorg, but without link to add reverse (since already added)
        $email = $this->getLastEmail('To: Test Org');        
        $this->assertContains('/testposter16/network', $email);
        $this->assertNotContains('/testorg', $email);
        
        // verify partnershps show up on public view of page
        $this->open('/testposter16/network');
        $this->mouseOver("//h3//a[contains(@href, 'testorg')]");          
        $this->mouseOver("//p[contains(text(),'whee reverse relationship')]");               
        
        // verify network latest updates from testorg
        $this->mouseOver("//a[@class='feed_org_name' and contains(@href,'testorg')]");
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), 'yay partners')]");
        $this->mouseOver("//div[@class='feed_snippet' and contains(text(), 'hooray partners')]");
        $this->mustNotExist("//div[@class='feed_snippet' and contains(text(), 'whee reverse relationship')]");        
        
        // verify feed items
        $this->open('/pg/feed');
        $this->mouseOver("//div[@class='feed_content' and .//a[contains(@href,'testorg')] and .//a[contains(@href,'testposter16')]]");
        $this->mouseOver("//div[@class='feed_content' and .//a[contains(@href,'testorg')] and .//a[contains(@href,'testposter15')]]");
        $this->mouseOver("//div[@class='feed_content' and .//a[contains(@href,'testorg')] and .//a[contains(@href,'testposter14')]]");
        $this->mouseOver("//div[@class='feed_content' and .//a[contains(@href,'testorg')] and .//a[contains(@href,'testposter13')]]");
        $this->mouseOver("//div[@class='feed_content' and .//a[contains(@href,'testorg')] and .//a[contains(@href,'testposter12')]]");
        $this->mustNotExist("//div[@class='feed_content' and .//a[contains(@href,'testorg')] and .//a[contains(@href,'testposter11')]]");        
        $this->mustNotExist("//div[@class='feed_content' and .//a[contains(@href,'testposter15')] and .//a[contains(@href,'testposter16')]]");                

        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
                                
        // make sure non-existent org shows up properly, and link in invite email goes to correct URL
        $networkLink = $this->getLinkFromText($inviteEmail, 0);
        $this->setUrl($networkLink);
        $this->mouseOver("//h3[contains(text(),'$invitedOrgName')]");
        $this->mouseOver("//a[@href='mailto:$invitedOrgEmail']");
        
        // create account with invite code
        
        $inviteUsername = "selenium".time();
        $inviteLink = $this->getLinkFromText($inviteEmail, 1);
        $realInviteName = "Selenium Org ".time();
        $this->setUrl($inviteLink);
        $this->check("//input[@value='np']");
        $this->check("//input[@value='tz']");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->assertEquals($invitedOrgName, $this->getValue("//input[@name='org_name']"));
        $this->type("//input[@name='org_name']", $realInviteName);
        $this->type("//input[@name='username']", $inviteUsername);
        $this->type("//input[@name='password']", 'password');
        $this->type("//input[@name='password2']", 'password');
        $this->assertEquals($invitedOrgEmail, $this->getValue("//input[@name='email']"));        
        $this->type("//input[@name='phone']", '1234567,2345678, +123143456789');
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->typeInFrame("//iframe", 'being invited');
        $this->check("//input[@name='sector[]' and @value='8']");
        $this->submitForm();
        $this->ensureGoodMessage();
                       
        // verify testorg (which invited the new user) is on network page already
        $this->clickAndWait("//ul[@class='todo_steps']//a[contains(@href,'page/network/edit')]");
        $this->mouseOver("//a[contains(@href, 'testorg')]"); 
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->mouseOver("//h3//a[contains(@href, 'testorg')]"); 

        // verify original relationship updated correctly to point to the newly registered org, but no link yet
        $this->open('/testorg/network');
        $this->mouseOver("//h3[contains(text(), '$realInviteName')]");         
        
        // invite another org to join (no email sent yet because not approved)
        $newInviteName = "lkadfjlakejflakejflakewj";
        $this->open("/$inviteUsername/page/network/edit");
        $this->clickAddRelationship();
        $this->type("//input[@name='name']", $newInviteName);
        $this->type("//input[@name='email']","nobody+".time()."@nowhere.org");
        $this->click("//button");
        $this->retry('mouseOver', array("//div[@class='modalBox']//input[@type='checkbox']"));
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//b[contains(text(), '$newInviteName')]");          
        
        // add an existing org in network (also no email sent yet)
        $this->clickAddRelationship();
        $this->type("//input[@name='name']", "Test Poster14");
        $this->click("//button");
        $this->retry('mouseOver', array("//span[@class='search_url' and contains(text(), 'testposter14')]"));
        $this->clickAndWait("//input[@type='submit']");
        $this->ensureGoodMessage();
        $this->mouseOver("//a[contains(@href, 'testposter14')]");          
        
        $this->open("/$inviteUsername/network");
        $this->mouseOver("//h3//a[contains(@href, 'testposter14')]");         
        $this->mouseOver("//h3[contains(text(), '$newInviteName')]");          
        
        // last email should be email notifying admins of new org, not any network notifications
        sleep(1);
        $adminEmail = $this->getLastEmail();
        $this->assertNotContains('/network', $adminEmail);
        $this->assertContains("New organization registered", $adminEmail);
                
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");                
                
        // log in as admin and approve
        $loginUrl = $this->getLinkFromText($adminEmail);
        $this->setUrl($loginUrl);
        $this->login('testadmin','testtest');
        $this->ensureGoodMessage();
        $this->click("//a[contains(@href,'approval=2')]");
        $this->getConfirmation();
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();
        
        // verify invite and notification emails get sent out when org is approved
        $email = $this->getLastEmail("$realInviteName has listed Test Poster14 as a partner");
        $this->assertContains($inviteUsername, $email);        

        $email = $this->getLastEmail($newInviteName);
        $this->assertContains($inviteUsername, $email);        

        // verify original relationship updated correctly to point to the newly registered org
        $this->open('/testorg/network');
        $this->mouseOver("//h3//a[contains(@href, '$inviteUsername')]");         

        // test inviting partner organizations to discussions
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");
        $this->open("/pg/login");
        $this->login('testorg','testtest');
        
        $this->open('/testorg/page/discussions/edit');
        $this->clickAndWait("//a[contains(@href,'topic/new')]");
        $this->type("//input[@name='subject']", "Test Topic");
        $this->typeInFrame("//iframe", "test message");
        $this->type("//input[@name='name']", "Test Name");
        $this->type("//input[@name='location']", "Test Location");
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->click("//div[@class='good_messages']//p//a");        
        
        $this->retry('selectShareWindow');
        $this->retry('click', array("//a[@id='add_partners']"));
        $this->retry('checkEmailEntered', array('nobody@nowhere.com'));
        $this->retry('checkEmailEntered', array($invitedOrgEmail));
        $this->retry('checkEmailEntered', array("p12"));
        $this->type("//textarea[@name='emails']", "nobody@nowhere.com; $invitedOrgEmail");
        
        $this->submitForm();
        $this->ensureBadMessage();
        $this->type("//input[@name='subject']", "Test Org invites you to a discussion");
        $this->type("//textarea[@name='message']", "hi!");        
        $this->submitForm();
        $this->ensureGoodMessage();
        $this->close();
        $this->selectWindow(null);                
        
        $email = $this->getLastEmail("Test Org invites you to a discussion");
        $this->assertContains('hi!', $email);
        $this->assertContains($this->getLocation(), $email);
        $this->assertContains($invitedOrgEmail, $email);
        $this->assertContains('nobody@nowhere.com', $email);
        $this->assertNotContains('+p12', $email);
        
        // can't invite same emails twice, but can invite others
        $this->click("//div[@class='shareLinks']//a[contains(@href,'emailShare')]");        
        
        $this->retry('selectShareWindow');
        
        $this->type("//textarea[@name='emails']", "foo@nowhere.com; nobody@nowhere.com");        
        $this->type("//textarea[@name='message']", "yo");                
        $this->submitForm();
        $this->ensureBadMessage(); // duplicate email
        $this->ensureGoodMessage(); // at least one valid recipient
        
        $this->close();
        $this->selectWindow(null);                
                
        $email = $this->getLastEmail("foo@nowhere.com");
        $this->assertContains('Test Org', $email);
        $this->assertContains($this->getLocation(), $email);
        $this->assertNotContains('nobody@nowhere.com', $email);
    }
        
    function checkEmailEntered($email)
    {
        $this->assertContains($email, $this->getValue("//textarea[@name='emails']"));
    }
                
        
    private function clickAddRelationship()
    {
        $this->clickAndWait("//a[contains(@href,'add_relationship')]");
    }
    
    private function clearNetworks()
    {
        $this->open('/pg/login');
        $this->login('testadmin','testtest');
        $this->ensureGoodMessage();
        
        $this->deleteNetwork('testorg');
        $this->deleteNetwork('testposter12');
        $this->deleteNetwork('testposter13');
        $this->deleteNetwork('testposter14');
        $this->deleteNetwork('testposter15');    
        $this->deleteNetwork('testposter16');    
    }    
    
    private function deleteNetwork($username)
    {
        $this->open("/{$username}/page/network/edit");
        while (true)
        {
            try
            {
                $this->click("//a[contains(@href,'delete_relationship')]");
            }
            catch (Exception $ex)
            {
                break;
            }
            
            $this->getConfirmation();
            $this->waitForPageToLoad(10000);
            $this->ensureGoodMessage();
        }

        try
        {
            $this->click("//button[@id='widget_delete']");
        }
        catch (Exception $ex)
        {
            return;
        }        
        $this->getConfirmation();        
        $this->waitForPageToLoad(10000);
        $this->ensureGoodMessage();        
    }    
    
}

