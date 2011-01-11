<?php

class EnvayaSiteTest extends SeleniumTest
{
    public function test()
    {        
        $this->_testContactForm();
    }

    private function _testContactForm()
    {
        $this->open("/page/contact");
        $this->type("//textarea[@name='message']", "contact message");
        $this->type("//input[@name='name']", "contact name");
        $this->type("//input[@name='email']", "adunar+bar@gmail.com");
        $this->submitForm();
        $this->mouseOver("//div[@class='good_messages']");

        $email = $this->getLastEmail("User feedback");

        $this->assertContains("contact message",$email);
        $this->assertContains('contact name', $email);
        $this->assertContains('adunar+bar@gmail.com', $email);
    }
}
