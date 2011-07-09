<?php

require_once 'phpwebdriver/WebDriver.php';

/**
 * 
 * @author kolec
 * @version 1.0
 * @property WebDriver $webdriver
 */
class PHPWebDriverTest extends PHPUnit_Framework_TestCase {

    private $test_url = "http://localhost:8080/php-webdriver-bindings/test_page.php";

    protected function setUp() {
        $this->webdriver = new WebDriver("localhost", 4444);
        $this->webdriver->connect("firefox");
    }

    protected function tearDown() {
        $this->webdriver->close();
    }

    public function testExecute() {
        $this->webdriver->get($this->test_url);
        $result = $this->webdriver->executeScript("return sayHello('unitTest')", array());
        $this->assertEquals("hello unitTest !!!", $result);
    }

    public function testScreenShot() {
        $this->webdriver->get($this->test_url);
        $tmp_filename = "screenshot".uniqid().".png";
        //unlink($tmp_filename);
        $result = $this->webdriver->getScreenshotAndSaveToFile($tmp_filename);
        $this->assertTrue(file_exists($tmp_filename));
        $this->assertTrue(filesize($tmp_filename)>100);
        unlink($tmp_filename);
    }

    /**
     * @expectedException WebDriverException
     */
    public function testHandleError() {
        $this->webdriver->get($this->test_url);
        $element = $this->webdriver->findElementBy(LocatorStrategy::name, "12323233233aa");
    }

    public function testFindElemenInElementAndSelections() {
        $this->webdriver->get($this->test_url);
        $element = $this->webdriver->findElementBy(LocatorStrategy::name, "sel1");
        $this->assertNotNull($element);
        $options = $element->findElementsBy(LocatorStrategy::tagName, "option");
        $this->assertNotNull($options);
        $this->assertNotNull($options[2]);
        $this->assertEquals($options[2]->getText(), "option 3");
        $this->assertFalse($options[2]->isSelected());
        $options[2]->setSelected();
        $this->assertTrue($options[2]->isSelected());
        $this->assertFalse($options[0]->isSelected());
    }

    public function testFindElementByAndSubmit() {
        $this->webdriver->get($this->test_url);
        $element = $this->webdriver->findElementBy(LocatorStrategy::id, "prod_name");
        $this->assertNotNull($element);
        $element->sendKeys(array("selenium 123"));
        $this->assertEquals($element->getValue(), "selenium 123");
        $element->clear();
        $this->assertEquals($element->getValue(), "");
        $element->sendKeys(array("selenium 123"));
        $element->submit();
        $element2 = $this->webdriver->findElementBy(LocatorStrategy::id, "result1");
        $this->assertNotNull($element2);
    }

    public function testGetPageAndUrl() {
        $this->webdriver->get($this->test_url);
        $this->assertEquals($this->webdriver->getTitle(), "Test page");
        $this->assertEquals($this->webdriver->getCurrentUrl(), $this->test_url);
    }

    public function testGetText() {
        $this->webdriver->get($this->test_url);
        $element = $this->webdriver->findElementBy(LocatorStrategy::name, "div1");
        $this->assertNotNull($element);
        $this->assertEquals($element->getText(), "lorem ipsum");
    }

    public function testGetName() {
        $this->webdriver->get($this->test_url);
        $element = $this->webdriver->findElementBy(LocatorStrategy::name, "div1");
        $this->assertNotNull($element);
        $this->assertEquals($element->getName(), "div");
    }

    public function testGetPageSource() {
        $this->webdriver->get($this->test_url);
        $src = $this->webdriver->getPageSource();
        $this->assertNotNull($src);
        $this->assertTrue(strpos($src, "<html>") == 0);
        $this->assertTrue(strpos($src, "<body>") > 0);
        $this->assertTrue(strpos($src, "div1") > 0);
    }

}

?>