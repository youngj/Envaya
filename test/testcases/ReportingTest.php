<?php

class ReportingTest extends SeleniumTest
{
    private $start_url;
    private $manage_url; 
    private $view_url;
    private $username;

    public function test()
    {        
        $this->_testNewReportDefinition();
        $this->_testCreateAccount();
        $this->_testExistingAccount();
        $this->_testCantViewBeforeApproved();
        $this->_testApproveResponses();
        $this->_testViewResponses();
    }
    
    private function _testCantViewBeforeApproved()
    {
        $this->open($this->view_url);
        $this->mustNotExist("//a[contains(@href,'testorg')]");
    }
    
    private function _testViewResponses()
    {
        $this->open($this->view_url);
        $this->clickAndWait("//a[contains(@href,'testorg')]");
        $this->assertContains("YAAAY", $this->getText("//div[@class='report_field_other_name']"));                        
        $this->assertEquals(preg_match('/\d+/',$this->getLocation(), $matches), 1);
        $report_id = $matches[0];
        
        // testorg should have reports tab
        $this->clickAndWait("//a[contains(@href,'testorg/reports')]");
        $this->clickAndWait("//a[contains(@href,'report/{$report_id}')]");
        $this->assertContains("YAAAY", $this->getText("//div[@class='report_field_other_name']"));                        
        
        $this->open($this->view_url);
        $this->clickAndWait("//a[contains(@href,'{$this->username}')]");
        
        $this->mouseOver("//div[@class='good_messages']"); // awaiting review
        $this->mustNotExist("//div[@class='report_field_other_name']");                
    }
    
    private function _testApproveResponses()
    {
        $this->open($this->manage_url);
        $this->type("//input[@name='username']",'testgrantmaker');
        $this->type("//input[@name='password']",'testtest');
        $this->submitForm();
        
        // test preview
        $this->clickAndWait("//div[@class='report_section_nav']//a[contains(text(),'Preview')]");
        $this->mouseOver("//input[@name='field_full_name']");
        $this->clickAndWait("//form//div[@class='report_section_nav']//a[contains(text(),'Challenges')]");
        $this->mouseOver("//textarea[@id='inputGrid0_challenge_1']");
          
        $this->clickAndWait("//a[contains(@href,'tab=manage')]");
               
        $this->clickAndWait("//a[contains(@href,'view_response') and contains(@href,'testorg')]");
        
        $this->assertContains("YAAAY", $this->getText("//div[@class='report_field_other_name']"));                        
        $this->clickAndWait("//a[contains(text(),'Approve Report')]");        
        $this->getConfirmation();
        
        $this->mouseOver("//a[contains(@href,'testorg') and contains(text(),'Remove Approval')]");
                
        $this->clickAndWait("//a[contains(@href,'status=12') and contains(@href,'{$this->username}')]");
        $this->getConfirmation();
        $this->mouseOver("//a[contains(@href,'{$this->username}') and contains(text(),'Remove Approval')]");

        $this->clickAndWait("//a[contains(@href,'tab=export')]");
        
        // TODO test csv file
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");        
    }
    
    private function _testNewReportDefinition()
    {
        $this->open('/testgrantmaker/reports/edit');
        $this->type("//input[@name='username']",'testgrantmaker');
        $this->type("//input[@name='password']",'testtest');
        $this->submitForm();
        $this->clickAndWait("//a[contains(@href,'reporting/add')]");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        $this->manage_url = $this->getLocation();
        
        $this->clickAndWait("//a[contains(@href,'/start')]");
        $this->start_url = $this->getLocation();
        
        $this->view_url = str_replace( "/start","", $this->start_url);
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");        
    }
    
    private function clickSave()
    {
        $this->submitForm("//div[@id='floating_save']//button");
    }
    
    private function _testExistingAccount()
    {
        $this->open($this->start_url);
        
        $this->clickAndWait("//div[@id='content_mid']//a[contains(@href,'pg/login')]");        
        $this->type("//input[@name='username']", 'testorg');
        $this->type("//input[@name='password']", 'testtest');
        $this->submitForm();
        
        $this->mouseOver("//div[@class='good_messages']");        
        $this->type("//input[@name='field_other_name']", 'YAAAY');
        $this->clickSave();
        $this->mouseOver("//input[@name='field_other_name' and @value='YAAAY']");        
        
        $this->clickAndWait("//div[@class='report_section_nav']//a[contains(text(),'Attachments')]");
        $this->submitForm();
        
        $this->check("//input[@id='confirm_box']");
        $this->type("//input[@id='signature']", "Mr. User");
        $this->submitForm();
        
        $this->assertContains("Report submitted", $this->getText("//h1"));
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");        
        
        $email = $this->getLastEmail("FCS Narrative Report submitted by Test Org");
        $this->assertContains('YAAAY', $email);

        sleep(2);
        $email = $this->getLastEmail("FCS Narrative Report submitted to Test Grantmaker");
        $this->assertContains('YAAAY', $email);
                
    }
    
    private function _testCreateAccount()
    {
        $this->open($this->start_url);
        $this->clickAndWait("//a[contains(@href,'/create_account')]");
        $this->submitForm();
        $this->mouseOver("//div[@class='bad_messages']");
            
        $this->username = "selenium".time();
        $this->type("//input[@name='org_name']", "Report Org ".time());
        $this->type("//input[@name='username']", $this->username);
        $this->type("//input[@name='password']", "password");
        $this->type("//input[@name='password2']", "password");
        $this->type("//input[@name='email']", "adunar+".time()."@gmail.com");
        $this->type("//input[@name='phone']", "555-1212");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->typeInFrame("//iframe", "testing the reporting system");
        $this->check("//input[@name='sector[]' and @value='1']");
        $this->check("//input[@name='sector[]' and @value='2']");
        
        $this->type("//input[@name='city']", "Dar es Salaam");
        $this->select("//select[@name='region']", "Dar es Salaam");
        $this->select("//select[@name='theme']", 'Beads');        
        
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->getLastEmail("New organization registered");
        
        // part 1: introduction
        
        $this->mouseOver("//input[@name='field_full_name' and contains(@value,'Report Org')]");
        $this->type("//input[@name='field_full_name']", 'Report Org 2.0');
        $this->type("//input[@name='field_other_name']", 'RO2');
        $this->clickSave();
        
        $this->mouseOver("//input[@name='field_full_name' and @value='Report Org 2.0']");
        $this->mouseOver("//input[@name='field_other_name' and @value='RO2']");        
        
        $this->type("//textarea[@name='field_project_coordinator']", 'Mr. Person');
        $this->submitForm();
        
        // part 2: project description
        
        $this->type("//input[@name='field_beneficiaries_female_direct']", "10");
        $this->s->fireEvent("//input[@name='field_beneficiaries_female_direct']", "keyup");
        
        $this->type("//input[@name='field_beneficiaries_male_direct']", "20");
        $this->s->fireEvent("//input[@name='field_beneficiaries_male_direct']", "keyup");
        
        sleep(1);
        
        $this->assertEquals($this->s->getValue("//input[@name='field_beneficiaries_direct']"), '30'); // automatically calculated field
        $this->type("//input[@name='field_beneficiaries_male_direct']", "fooo"); // not a number
        
        $this->s->fireEvent("//input[@name='field_beneficiaries_male_direct']", "keyup");        
        sleep(1);
        
        $this->assertEquals($this->s->getValue("//input[@name='field_beneficiaries_direct']"), '30');
        
        $this->select("//select[@id='inputGrid0_region_1']", "Iringa");
        $this->type("//input[@id='inputGrid0_district_1']", "Iringa Urban");
        $this->type("//input[@id='inputGrid0_total_1']", "22");
        
        $this->select("//select[@id='inputGrid0_region_2']", "Lindi");
        $this->type("//input[@id='inputGrid0_total_2']", "8");
        
        $this->check("//input[@name='field_thematic_areas[]' and @value='policy']");
        $this->check("//input[@name='field_thematic_areas[]' and @value='capacity']");
        
        $this->clickSave();
        
        $this->mouseOver("//input[@name='field_thematic_areas[]' and @value='policy' and @checked='checked']");
        $this->mouseOver("//input[@name='field_thematic_areas[]' and @value='capacity' and @checked='checked']");
        
        $this->assertEquals($this->getSelectedLabel("//select[@id='inputGrid0_region_1']"), "Iringa");
        $this->mouseOver("//input[@id='inputGrid0_district_1' and @value='Iringa Urban']");
        $this->mouseOver("//input[@id='inputGrid0_total_1' and @value='22']");
        $this->type("//input[@id='inputGrid0_total_1']", "23");
        
        $this->assertEquals($this->getSelectedLabel("//select[@id='inputGrid0_region_2']"), "Lindi");
        $this->mouseOver("//input[@id='inputGrid0_total_2' and @value='8']");
        
        $this->mouseOver("//input[@name='field_beneficiaries_female_direct' and @value='10']");
        $this->mouseOver("//input[@name='field_beneficiaries_male_direct' and @value='fooo']");
        $this->mouseOver("//input[@name='field_beneficiaries_direct' and @value='30']");        
        
        // changing sections via nav links should save responses
        $this->clickAndWait("//div[@class='report_section_nav']//a[contains(text(),'Linkages')]");
        
        // logout and resume later
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");        
        
        $this->open($this->start_url);
        
        $this->clickAndWait("//div[@id='content_mid']//a[contains(@href,'pg/login')]");        
        $this->type("//input[@name='username']", $this->username);
        $this->type("//input[@name='password']", 'password');
        $this->submitForm();
        
        $this->mouseOver("//div[@class='good_messages']");
        
        $this->clickAndWait("//div[@class='report_section_nav']//a[contains(text(),'Verify Responses')]");
        
        // verify responses
        
        $this->assertContains("Report Org 2.0", $this->getText("//div[@class='report_field_full_name']"));
        $this->assertContains("RO2", $this->getText("//div[@class='report_field_other_name']"));
        $this->assertContains("(No Response)", $this->getText("//div[@class='report_field_project_name']"));        
        $this->assertContains("Mr. Person", $this->getText("//div[@class='report_field_project_coordinator']"));    
        
        $this->assertContains("Policy Engagement", $this->getText("//div[@class='report_field_thematic_areas']"));
        $this->assertContains("Civil Society Capacity Strengthening", $this->getText("//div[@class='report_field_thematic_areas']"));
        
        $this->assertContains("Iringa", $this->getText("//div[@class='report_field_regions']//tbody/tr[1]/td[1]"));
        $this->assertContains("Iringa Urban", $this->getText("//div[@class='report_field_regions']//tbody/tr[1]/td[2]"));
        $this->assertContains("23", $this->getText("//div[@class='report_field_regions']//tbody/tr[1]/td[5]"));
        $this->assertContains("Lindi", $this->getText("//div[@class='report_field_regions']//tbody/tr[2]/td[1]"));
        $this->assertContains("8", $this->getText("//div[@class='report_field_regions']//tbody/tr[2]/td[5]"));
        
        $this->assertContains("10", $this->getText("//div[@class='report_field_total_beneficiaries']//tbody/tr[2]/td[1]"));
        $this->assertContains("fooo", $this->getText("//div[@class='report_field_total_beneficiaries']//tbody/tr[3]/td[1]"));
        $this->assertContains("30", $this->getText("//div[@class='report_field_total_beneficiaries']//tbody/tr[4]/td[1]"));
        
        $this->check("//input[@id='confirm_box']");
        $this->type("//input[@id='signature']", "Mr. User");
        $this->submitForm();
        
        $this->assertContains("Report submitted", $this->getText("//h1"));
        
        $this->clickAndWait("//a[contains(@href,'pg/logout')]");        
        
        $email = $this->getLastEmail("FCS Narrative Report submitted by Report Org");
        $this->assertContains('Iringa', $email);        
    }
}