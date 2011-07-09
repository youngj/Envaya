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

require_once 'WebElement.php';
require_once 'WebDriverResponseStatus.php';
require_once 'WebDriverException.php';
require_once 'NoSuchElementException.php';

class WebDriverBase 
{
    protected $requestURL;

    function __construct($requestURL) 
    {
        $this->requestURL = $requestURL;
    }
	
	protected function &curlInit( $url ) 
    {
        $url = $this->requestURL . $url;
        
        $curl = curl_init( $url );

		curl_setopt( $curl, CURLOPT_HTTPHEADER, array("application/json;charset=UTF-8"));
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		return $curl;
	}

    protected function preparePost($session, $postData = null)
    {
        curl_setopt($session, CURLOPT_POST, true);
        
        if (!$postData) 
        {
            $postData = array();
        }
        
        curl_setopt($session, CURLOPT_POSTFIELDS, json_encode($postData));        
    }
    
    protected function doPostRequest($url, $postData = null) 
    {
        $session = $this->curlInit($url);
        $this->preparePost($session, $postData);
        $res = $this->doRestRequest($session);                       
        curl_close($session);
        return $res;
    }
    
    protected function doGetRequest($url) 
    {
        $session = $this->curlInit($url);   
        $res = $this->doRestRequest($session);               
        curl_close($session);
        return $res;
    }    
    
    protected function doRestRequest($session)
    {
        $result_str = curl_exec($session);
        if ($result_str === false)
        {
            throw new WebDriverException("Could not connect to Selenium server");
        }
        
        if ($result_str === '')
        {
            return null;
        }
        
        $result = json_decode(trim($result_str), true);    
        if (!$result)
        {
            throw new WebDriverException("Invalid response from Selenium server");
        }        
        
        switch ($result['status'])
        {
            case WebDriverResponseStatus::Success:
                return $result['value'];            
            case WebDriverResponseStatus::NoSuchElement:
                throw new NoSuchElementException();
            default:
                throw new WebDriverException(
                    @$result['value']['message'] ?: @$result['value']['class'], $result['status']);
        }
    }

    protected function doDeleteRequest($url) 
    {
        $session = $this->curlInit($url);    
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE');        
        curl_exec($session);
        curl_close($session);
    }
    
    /**
     * Search for an element on the page, starting from the document root. 
     * @param string $locatorStrategy
     * @param string $value
     * @return WebElement found element
     */
    public function findElementBy($locatorStrategy, $value) 
    {    
        $element = $this->doPostRequest("/element", array('using' => $locatorStrategy, 'value' => $value));        
        return new WebElement($this, $element, null);
    }

    /**
     * 	Search for multiple elements on the page, starting from the document root. 
     * @param string $locatorStrategy
     * @param string $value
     * @return array of WebElement
     */
    public function findElementsBy($locatorStrategy, $value) 
    {
        $elements = $this->doPostRequest("/elements", array('using' => $locatorStrategy, 'value' => $value));

        $webelements = array();
        foreach ($elements as $key => $element) {
            $webelements[] = new WebElement($this, $element, null);
        }
        return $webelements;
    }
}
