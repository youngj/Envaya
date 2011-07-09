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

class WebElement extends WebDriverBase 
{
    function __construct($parent, $element, $options) 
    {    
        if (get_class($parent) == 'WebDriver') 
        {
            $root = $parent->requestURL;
        } 
        else 
        {
            $root = preg_replace("(/element/.*)", "", $parent->requestURL);
        }
        parent::__construct($root . "/element/" . $element['ELEMENT']);
    }    

    public function sendKeys($value) 
    {
        if (!is_array($value)) 
        {
            $value = array($value);
        }       
        
        return $this->doPostRequest("/value", array('value' => $value));
    }

    public function getValue() 
    {
        return $this->doGetRequest("/value");
    }

    public function clear() 
    {
        $this->doPostRequest("/clear");
    }

    public function click() 
    {
        $this->doPostRequest("/click");
    }

    public function submit() 
    {
        $this->doPostRequest("/submit");
    }

    public function getText() 
    {
        return $this->doGetRequest("/text");
    }

    public function getName() 
    {
        return $this->doGetRequest("/name");
    }

    /**
     * Determine if an OPTION element, or an INPUT element of type checkbox or radiobutton is currently selected.
     * @return boolean Whether the element is selected.
     */
    public function isSelected() 
    {
        return $this->doGetRequest("/selected") == 'true';
    }

    /**
     * Select an OPTION element, or an INPUT element of type checkbox or radiobutton.
     * 
     */
    public function setSelected() 
    {
        return $this->doPostRequest("/selected");
    }

    /**
     * Determine if an element is currently enabled
     * @return boolean Whether the element is enabled.
     */
    public function isEnabled() 
    {
        return $this->doGetRequest("/enabled") == 'true';
    }

    public function getAttribute($name)
    {
        return $this->doGetRequest("/attribute/$name");
    }
}

?>