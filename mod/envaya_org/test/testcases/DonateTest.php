<?php

class DonateTest extends WebDriverTest
{
    public function test()
    {        
        $this->open("/envaya/page/contribute");
        
        // test error missing information
        $this->waitForElement("//input[@name='_amount' and @value='50']")->click();
        $this->type("//input[@name='full_name']", "Test User");
        $this->submitForm();
        $this->retry('ensureBadMessage');
        
        $this->type("//input[@name='email']", "nobody@nowhere.com");
        $this->type("//input[@name='phone']", "650-555-1212");
        $this->type("//input[@name='company']", "Example Company");
        $this->type("//input[@name='website']", "www.example.com");
        $this->type("//input[@name='address']", "21 Jump Street");
        $this->type("//input[@name='address2']", "Suite #1138");
        $this->type("//input[@name='city']", "New York");
        $this->type("//input[@name='state']", "NY");
        $this->type("//input[@name='zip']", "10001");
        $this->type("//input[@name='country']", "USA");
        $this->submitForm();
        
        // test envaya admins notified
        $email = $this->getLastEmail("650-555-1212");
        $this->assertContains("Test User", $email);        
        
        // test information passed to TCI form
        $this->waitForElement("//div[@class='informationConfirm']");
        $this->mustExist("//div[@class='informationConfirm']//strong[contains(text(),'$50.00')]");
        $this->mustExist("//div[@class='informationConfirm']//strong[contains(text(),'Envaya')]");
        
        $this->mustExist("//div[@class='informationConfirm' and contains(text(),'Test User')]");
        $this->mustExist("//input[@name='x_first_name' and @value='Test']");
        $this->mustExist("//input[@name='x_last_name' and @value='User']");
        $this->mustExist("//input[@name='x_company' and @value='Example Company']");
        $this->mustExist("//input[@name='x_address' and @value='21 Jump Street Suite #1138']");
        $this->mustExist("//input[@name='x_city' and @value='New York']");
        $this->mustExist("//input[@name='x_state' and @value='NY']");
        $this->mustExist("//input[@name='x_zip' and @value='10001']");
        $this->mustExist("//input[@name='x_phone' and @value='650.555.1212']");
        $this->mustExist("//input[@name='x_email' and @value='nobody@nowhere.com']");
        $this->mustExist("//input[@name='x_amount' and @value='50']");

        // test other amount        
        $this->open("/envaya/page/contribute");
        $this->waitForElement("//input[@name='_other_amount']");
        $this->type("//input[@name='_other_amount']", "153");
        $this->type("//input[@name='full_name']", "Test Name Two");
        
        $this->type("//input[@name='email']", "nobody@nowhere.com");
        $this->type("//input[@name='phone']", "(650) 555-1213");
        $this->type("//input[@name='address']", "22 Jump Street");
        $this->type("//input[@name='city']", "Seattle");
        $this->type("//input[@name='state']", "WA");
        $this->type("//input[@name='zip']", "98101");
        $this->type("//input[@name='country']", "USA");        
        
        $this->submitForm();
        
        $email = $this->getLastEmail("Test Name Two");
        $this->assertContains("153", $email);        
        
        $this->waitForElement("//div[@class='informationConfirm']");
        $this->mustExist("//input[@name='x_first_name' and @value='Test Name']");
        $this->mustExist("//input[@name='x_last_name' and @value='Two']");
        $this->mustExist("//input[@name='x_phone' and @value='650.555.1213']");
        $this->mustExist("//input[@name='x_amount' and @value='153']");
        
        $this->mustExist("//div[@class='informationConfirm']//strong[contains(text(),'$153.00')]");
        $this->mustExist("//div[@class='informationConfirm']//strong[contains(text(),'Envaya')]");               
    }
}
