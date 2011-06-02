<?php

class UploadTest extends SeleniumTest
{
    private $post_content;

    public function init_selenium()
    {
        // Selenium attachFile only works with *chrome browser
        return new Testing_Selenium("*chrome", "http://localhost");    
    }
    
    public function test()
    {        
        $this->_testNewsUpdateImage();
        $this->_testAddPhotos();
        $this->_testLogo();
        $this->_testMobileUpload();            
    }   
    
    private function _testNewsUpdateImage()
    {
        $this->open("/pg/login");
        $this->login('testorg','testtest');
        
        $this->click("//div[@class='attachControls']//a");
        
        $this->retry('selectFrame', array("//iframe[contains(@src,'select_image')]"));
        
        $this->selectUploadFrame();
        
        $this->attachFile("//input[@type='file']","http://localhost/_media/images/test/1.jpg");
        
        $this->selectFrame("relative=parent");
        
        $this->retry('mustBeVisible', array("//div[@id='imageOptionsContainer']"));
        
        $this->check("//input[@value='medium']");
        $this->check("//input[@value='right']");        
        
        $this->selectFrame("relative=top");
        
        $this->click("//input[@type='submit' and @value='OK']");
        sleep(1);
        
        $this->submitForm();
        
        $this->mouseOver("//img[contains(@src,'/medium.jpg') and @class='image_right']");
        
        $imgUrl = $this->getAttribute("//div[@id='content']//img[contains(@src,'/medium.jpg')]@src");
        
        $this->checkImage($imgUrl, 10000, 25000);
                
        $this->mustNotExist("//img[contains(@src,'/large.jpg')]"); 
        $this->clickAndWait("//div[@id='content']//img[contains(@src,'/medium.jpg')]");
        $this->mouseOver("//img[contains(@src,'/large.jpg')]");
        
        $largeImgUrl = $this->getAttribute("//img[contains(@src,'/large.jpg')]@src");
        
        $this->checkImage($largeImgUrl, 25000, 75000);        
        
        $this->open("/testorg");
        
        $this->mouseOver("//img[contains(@src,'/small.jpg')]");
        
        $smallImgUrl = $this->getAttribute("//img[contains(@src,'/small.jpg')]@src");
        
        $this->checkImage($smallImgUrl, 1000, 10000);        
    }
      
    private function _testAddPhotos()
    {
        $this->open("/testorg/dashboard");
        $this->clickAndWait("//a[contains(@href,'/addphotos')]");
        
        // test errors for iimages
        $this->selectUploadFrame();        
        $this->attachFile("//input[@type='file']","http://localhost/_media/images/test/bad.jpg");        
        $this->selectFrame("relative=parent");
        
        $this->retry('mustBeVisible', array("//div[@id='progressContainer' and contains(text(), 'could not understand')]"));
        
        $this->selectUploadFrame();        
        $this->attachFile("//input[@type='file']","http://localhost/_media/images/test/3.jpg");
        $this->selectFrame("relative=parent");
        
        $this->retry('mustBeVisible', array("//div[@class='photoPreview']//img"));
        
        $imgUrl = $this->getAttribute("//div[@class='photoPreview']//img@src");        
        $this->checkImage($imgUrl, 2000, 10000); 

        $this->type("//textarea[@class='photoCaptionInput']","caption 3");
        
        $this->selectUploadFrame();        
        $this->attachFile("//input[@type='file']","http://localhost/_media/images/test/1.jpg");
        $this->selectFrame("relative=parent");        
        
        $this->retry('mustBeVisible', array("//div[@class='photoPreviewContainer'][2]//div[@class='photoPreview']//img"));
        
        $imgUrl = $this->getAttribute("//div[@class='photoPreviewContainer'][2]//div[@class='photoPreview']//img@src");        
        $this->checkImage($imgUrl, 2000, 10000); 

        $this->type("//div[@class='photoPreviewContainer'][2]//textarea[@class='photoCaptionInput']","caption 4");
        
        $this->submitForm();
               
        $this->mouseOver("//div[@class='section_content padded']//p[contains(text(),'caption 3')]");
        $this->mouseOver("//div[@class='section_content padded']//p[contains(text(),'caption 4')]");
                
        $this->clickAndWait("//div[@class='blog_date']//a");
        
        $this->mouseOver("//div[@class='section_content padded']//p[contains(text(),'caption 4')]");
        $this->mouseOver("//div[@class='section_content padded']//img");
        
        $imgUrl = $this->getAttribute("//div[@class='section_content padded']//img@src");        
        $this->checkImage($imgUrl, 20000, 100000);         
    }
    
    private function _testLogo()
    {
        $this->open("/testorg/dashboard");
        $this->clickAndWait("//a[contains(@href,'/design')]");
        $this->selectUploadFrame();
        
        $this->attachFile("//input[@type='file']","http://localhost/_media/images/test/logo.png");
        
        $this->selectFrame("relative=parent");
        
        $this->retry('mustBeVisible', array("//div[@class='imageUploadProgress']//img"));
        
        $this->submitForm();
        
        $this->ensureGoodMessage();
        $this->mouseOver("//table[@id='heading']//img[contains(@src,'medium.jpg')]");
        
        $imgUrl = $this->getAttribute("//table[@id='heading']//img[contains(@src,'medium.jpg')]@src");
        
        $this->checkImage($imgUrl, 2000, 10000);    
        
        $this->clickAndWait("//a[contains(@href,'org/feed')]");
        
        $this->mouseOver("//a[@class='feed_org_icon' and contains(@href,'/testorg')]//img");
        
        $smallImgUrl = $this->getAttribute("//a[@class='feed_org_icon' and contains(@href,'/testorg')]//img@src");
        
        $this->checkImage($smallImgUrl, 500, 2000);   

        $this->open("/testorg/design");        
        
        $this->check("//input[@type='checkbox']"); // remove image
        $this->submitForm();
        
        $this->mustNotExist("//table[@id='heading']//img[contains(@src,'medium.jpg')]");
        $this->mouseOver("//table[@id='heading']//img[contains(@src,'/staticmap')]");
        
        $staticMapUrl = $this->getAttribute("//table[@id='heading']//img[contains(@src,'/staticmap')]@src");
        
        $this->checkImage($staticMapUrl, 2000, 10000);   
    }
    
    private function _testMobileUpload()
    {
        $this->open("/");
        $this->clickAndWait("//a[contains(@href,'/dashboard')]");
        $this->clickAndWait("//a[contains(@href,'view=mobile')]");
        $this->clickAndWait("//a[contains(@href,'/addphotos')]");
        
        $this->attachFile("//input[@name='imageFile1']","http://localhost/_media/images/test/2.jpg");
        $this->type("//textarea[@name='imageCaption1']", "example caption");
        
        $this->clickAndWait("//input[@type='submit']");
        
        $this->mouseOver("//div[@class='section_content padded']//img[contains(@src,'large.jpg')]");
        $this->assertContains("example caption", $this->getText("//div[@class='section_content padded']"));
        
        $imgUrl = $this->getAttribute("//img[contains(@src,'/large.jpg')]@src");
        
        $this->checkImage($imgUrl, 10000, 100000);        
    }
}
