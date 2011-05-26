<?php

/*
 * Represents a HTTP response from an external URL.
 */
class Web_Response
{
    public $url;
    public $status;
    public $headers;
    public $content;
    
    private $dom = false;
   
    function get_dom()
    {
        if ($this->dom === false)
        {
            $dom = new DOMDocument;
            $status = @$dom->loadHTML($this->content);                
            if ($status)
            {
                $this->dom = $dom;
            }
            else
            {
                $this->dom = null;
            }   
        }
        return $this->dom;
    }    
    
    function get_title()
    {
        $dom = $this->get_dom();
        if (!$dom)
        {
            return null;            
        }
        
        $titles = $dom->getElementsByTagName('title');
        if ($titles->length)
        {
            return $titles->item(0)->textContent;
        }
        return null;
    
    }
}