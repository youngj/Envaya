<?php

/*
  Copyright 2011 3e software house & interactive agency

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
 */

require_once 'WebDriverBase.php';
require_once 'WebElement.php';
require_once 'WebDriverException.php';
require_once 'LocatorStrategy.php';

class WebDriver extends WebDriverBase {

    protected $baseURL;

    function __construct($host, $port) 
    {
        $this->baseURL = "http://" . $host . ":" . $port . "/wd/hub";
        parent::__construct($this->baseURL);
    }

    public function connect($capabilities = null) 
    {        
        $session = $this->curlInit("/session");                        
                        
        $default_capabilities = array(
            'browserName' => 'firefox',
            'version' => '',
            'javascriptEnabled' => true,
            'nativeEvents' => false,
        );

        $capabilities = array_merge($default_capabilities, $capabilities ?: array());
                        
        $this->preparePost($session, array(
            'desiredCapabilities' => $capabilities
        ));
        
        curl_exec($session);
        $header = curl_getinfo($session);
        $this->requestURL = $header['url'];
        curl_close($session);
    }
    
    /**
     * Delete the session.
     */
    public function close() 
    {
        return $this->doDeleteRequest('');
    }

    /**
     * Navigate to a new URL
     * @param string $url The URL to navigate to.
     */
    public function get($url) 
    {
        return $this->doPostRequest("/url", array(
            'url' => $url
        ));
    }

    /**
     * Get the current page title.
     * @return string The current URL.
     */
    public function getCurrentUrl() 
    {
        return $this->doGetRequest("/url");
    }

    /**
     * Get the current page title. 
     * @return string current page title
     */
    public function getTitle() 
    {
        return $this->doGetRequest("/title");
    }

    /**
     * Get the current page source.
     * @return string page source 
     */
    public function getPageSource() 
    {
        return $this->doGetRequest("/source");
    }

    /**
     * Get the current user input speed. The server should return one of {SLOW|MEDIUM|FAST}.
     * How these constants map to actual input speed is still browser specific and not covered by the wire protocol.
     * @return string {SLOW|MEDIUM|FAST}
     */
    public function getSpeed() 
    {
        return $this->doGetRequest("/speed");
    }

    public function setSpeed($speed) 
    {
        return $this->doPostRequest("/speed", array('speed' => $speed));        
    }

    /**
	Change focus to another window. The window to change focus to may be specified 
	by its server assigned window handle, or by the value of its name attribute.
    */
    public function selectWindow($windowName) 
    {
        return $this->doPostRequest("/window", array('name' => $windowName));        
    }

    public function closeWindow() 
    {
        return $this->doDeleteRequest("/window");        
    }
    
    public function refresh() 
    {
        return $this->doPostRequest("/refresh");        
    }

    public function selectFrame($frameId)
    {
        return $this->doPostRequest("/frame", array('id' => $frameId));        
    }
    
    /**
      Inject a snippet of JavaScript into the page for execution in the context of the currently selected frame.
     * The executed script is assumed to be synchronous and the result of evaluating the script
     * is returned to the client.
     * @return Object result of evaluating the script is returned to the client.
     */
    public function executeScript($script, $script_args) 
    {
        return $this->doPostRequest("/execute", array('script' => $script, 'args' => $script_args));
    }

    /**
      Inject a snippet of JavaScript into the page for execution
     * in the context of the currently selected frame. The executed script
     * is assumed to be asynchronous and must signal that is done by invoking
     * the provided callback, which is always provided as the final argument
     * to the function. The value to this callback will be returned to the client.
     * @return Object result of evaluating the script is returned to the client.
     */
    public function executeAsyncScript($script, $script_args) 
    {
        return $this->doPostRequest("/execute_async", array('script' => $script, 'args' => $script_args));
    }

    /**
     * Take a screenshot of the current page.
     * @return string The screenshot as a base64 encoded PNG.
     */
    public function getScreenshot() 
    {
        return $this->doGetRequest("/screenshot");
    }

    /**
     * Take a screenshot of the current page and saves it to png file.
     * @param $png_filename filename (with path) where file has to be saved
     * @return bool result of operation (false if failure)
     */
    public function getScreenshotAndSaveToFile($png_filename) {
        $img = $this->getScreenshot();
        $data = base64_decode($img);
        $success = file_put_contents($png_filename, $data);
    }

    public function getAlertText()
    {
        return $this->doGetRequest("/alert_text");
    }    

    
    public function acceptAlert()
    {
        return $this->doPostRequest("/accept_alert");
    }    
    
    public function dismissAlert()
    {
        return $this->doPostRequest("/dismiss_alert");
    }
}

