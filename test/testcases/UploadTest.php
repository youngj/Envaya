<?php

class UploadTest extends WebDriverTest
{
    private $post_content;

    public function test()
    {        
        $this->open("/pg/login");
        $this->login('testorg','testtest');
    
        $this->_testCustomHeader();
        $this->_testNewsUpdateImage();
        $this->_testAddPhotos();
        $this->_testLogo();
        $this->_testMobileUpload();            
    }   
    
    private function _testNewsUpdateImage()
    {        
        $this->open('/testorg/dashboard');
        $this->retry('click', array("//div[@class='attachControls']//a"));
        
        $this->retry('selectFrame', array("//iframe[contains(@src,'select_image')]"));
        
        $this->attachFile("//input[@type='file']", dirname(__DIR__)."/images/1.jpg");
                
        $this->retry('mustBeVisible', array("//div[@id='imageOptionsContainer']"));
        
        $this->click("//input[@value='medium']");
        $this->click("//input[@value='right']");        
        
        $this->selectFrame(null);
        
        $this->click("//input[@type='submit' and @value='OK']");
        sleep(1);
        
        $this->submitForm();
        
        $this->waitForElement("//img[contains(@src,'/medium.jpg') and @class='image_right']");
        
        $imgUrl = $this->xpath("//div[@id='content']//img[contains(@src,'/medium.jpg')]")->getAttribute("src");
        
        $this->checkImage($imgUrl, 10000, 25000);
                
        $this->mustNotExist("//img[contains(@src,'/large.jpg')]"); 
        $this->click("//div[@id='content']//img[contains(@src,'/medium.jpg')]");
        $this->waitForElement("//img[contains(@src,'/large.jpg')]");
        
        $largeImgUrl = $this->xpath("//img[contains(@src,'/large.jpg')]")->getAttribute("src");
        
        $this->checkImage($largeImgUrl, 25000, 75000);        
        
        $this->open("/testorg");
        
        $this->waitForElement("//img[contains(@src,'/small.jpg')]");
        
        $smallImgUrl = $this->xpath("//img[contains(@src,'/small.jpg')]")->getAttribute('src');
        
        $this->checkImage($smallImgUrl, 1000, 10000);        
    }
      
    private function _testAddPhotos()
    {
        $this->open("/testorg/dashboard");
        $this->waitForElement("//a[contains(@href,'/addphotos')]");
        $this->click("//a[contains(@href,'/addphotos')]");
        
        // test errors for images

        $this->attachFile("//input[@type='file']", dirname(__DIR__)."/images/bad.jpg");        
        
        $this->retry('mustBeVisible', array("//div[@id='progressContainer' and contains(text(), 'could not understand')]"));
        
        $this->attachFile("//input[@type='file']", dirname(__DIR__)."/images/3.jpg");
        
        $this->retry('mustBeVisible', array("//div[@class='photoPreview']//img"));
        
        $imgUrl = $this->xpath("//div[@class='photoPreview']//img")->getAttribute('src');        
        $this->checkImage($imgUrl, 2000, 10000); 

        $this->type("//textarea[@class='photoCaptionInput']","caption 3");
        
        $this->attachFile("//input[@type='file']",dirname(__DIR__)."/images/1.jpg");
        
        $this->retry('mustBeVisible', array("//div[@class='photoPreviewContainer'][2]//div[@class='photoPreview']//img"));
        
        $imgUrl = $this->xpath("//div[@class='photoPreviewContainer'][2]//div[@class='photoPreview']//img")->getAttribute('src');        
        $this->checkImage($imgUrl, 2000, 10000); 

        $this->type("//div[@class='photoPreviewContainer'][2]//textarea[@class='photoCaptionInput']","caption 4");
        
        $this->submitForm();
               
        $this->waitForElement("//div[@class='section_content padded']//p[contains(text(),'caption 3')]");
        $this->mouseOver("//div[@class='section_content padded']//p[contains(text(),'caption 4')]");
                
        $this->click("//div[@class='blog_date']//a");
        $this->waitForElement("//textarea[@name='content']");
        
        $this->mouseOver("//div[@class='section_content padded']//p[contains(text(),'caption 4')]");
        $this->mouseOver("//div[@class='section_content padded']//img");
        
        $imgUrl = $this->xpath("//div[@class='section_content padded']//img")->getAttribute('src');        
        $this->checkImage($imgUrl, 20000, 100000);         
    }
    
    private function _testLogo()
    {
        $this->open("/testorg/dashboard");
        $this->waitForElement("//a[contains(@href,'/design')]")->click();
        
        $this->attachFile("//input[@type='file']", dirname(__DIR__)."/images/logo.png");
        
        $this->retry('mustBeVisible', array("//div[@class='imageUploadProgress']//img"));
        
        $this->submitForm();
        
        $this->retry('ensureGoodMessage', array('design saved'));
        
        $imgUrl = $this->waitForElement("//table[@id='heading']//img[contains(@src,'medium.jpg')]")->getAttribute('src');
        
        $this->checkImage($imgUrl, 2000, 10000);    
        
        $this->click("//a[contains(@href,'pg/feed')]");

        $smallImgUrl = $this->waitForElement("//a[@class='feed_org_icon' and contains(@href,'/testorg')]//img")->getAttribute('src');
        
        $this->checkImage($smallImgUrl, 500, 2000);   

        $this->open("/testorg/design");        
        
        $this->waitForElement("//input[@type='checkbox']")->click(); // remove image
        $this->submitForm();
        
        $this->waitForElement("//table[@id='heading']//img[contains(@src,'/staticmap')]");
        $this->mustNotExist("//table[@id='heading']//img[contains(@src,'medium.jpg')]");
        
        $staticMapUrl = $this->xpath("//table[@id='heading']//img[contains(@src,'/staticmap')]")->getAttribute('src');
        
        $this->checkImage($staticMapUrl, 2000, 10000);   
    }
    
    private function _testCustomHeader()
    {
        $this->open("/testorg");
        
        $this->waitForElement("//h3[contains(text(),'a test organization')]");        
        $this->mustExist("//div[@class='shareLinks']//a");                
        
        $this->open("/testorg/design");
        
        $tagline = $this->waitForElement("//input[@name='tagline']");
        $tagline->clear();
        $tagline->sendKeys("custom tagline");
        
        $this->click("//input[@value='email']");
        $this->click("//input[@value='facebook']");
        $this->click("//input[@value='twitter']");
        
        $this->submitForm();
        $this->waitForElement("//h3[contains(text(),'custom tagline')]");
        $this->mustNotExist("//div[@class='shareLinks']//a");     

        $this->open("/testorg/design");        
        
        $this->waitForElement("//input[@name='custom_header' and @value='1']")->click();
        $this->attachFile("//div[@id='custom_header_container']//input[@type='file']", dirname(__DIR__)."/images/logo.png");        
        $this->retry('mustBeVisible', array("//div[@class='imageUploadProgress']//img"));
                
        $this->submitForm();
        
        $img = $this->waitForElement("//div[@class='heading_container']//img[contains(@src,'large.jpg')]");
        $this->assertEquals('150', $img->getAttribute('height'));
        $this->assertEquals('589', $img->getAttribute('width'));
        
        $this->checkImage($img->getAttribute('src'), 30000, 50000);    
        
        $this->open("/testorg/design");        
        
        $this->waitForElement("//input[@name='custom_header' and @value='0']")->click();        
        $this->submitForm();
        
        $this->waitForElement("//h3[contains(text(),'custom tagline')]");
        $this->mustNotExist("//div[@class='shareLinks']//a");             
        $this->mustNotExist("//div[@class='heading_container']//img[contains(@src,'large.jpg')]");
    }
    
    private function _testMobileUpload()
    {
        $this->open("/");
        $this->click("//a[contains(@href,'/dashboard')]");
        $this->waitForElement("//a[contains(@href,'/addphotos')]");
        $this->click("//a[contains(@href,'view=mobile')]");
        $this->waitForElement("//a[contains(@href,'view=default')]");
        $this->click("//a[contains(@href,'/addphotos')]");
        
        $this->waitForElement("//input[@name='imageFile1']");
        
        $this->attachFile("//input[@name='imageFile1']", dirname(__DIR__)."/images/2.jpg");
        $this->type("//textarea[@name='imageCaption1']", "example caption");
        
        $this->click("//input[@type='submit']");
        
        $this->waitForElement("//div[@class='section_content padded']//img[contains(@src,'large.jpg')]");
        $this->assertContains("example caption", $this->getText("//div[@class='section_content padded']"));
        
        $imgUrl = $this->xpath("//img[contains(@src,'/large.jpg')]")->getAttribute("src");
        
        $this->checkImage($imgUrl, 10000, 100000);        
    }
}
