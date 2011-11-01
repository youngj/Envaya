<?php

/*
 * The deflate() method strips out attributes and unnecessary whitespace from HTML, replacing
 * attributes with a single id attribute. 
 *
 * The inflate() method replaces id attributes assigned by a previous call to deflate(), and 
 * puts the original attributes back in. 
 * 
 * This is used to minimize the number of characters sent to the Google Translate API, which
 * costs $1 per 50,000 characters.
 */
class Markup_HtmlDeflater
{
    private $stored_attributes = array();
    private $next_id = 0;    

    function deflate($html)
    {       
        $dom = $this->get_dom($html);
        
        $body = $dom->getElementsByTagName('body')->item(0);
        
        $this->_deflate($body);
        
        return $this->get_body_html($dom);
    }
    
    private function get_dom($html)
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');        
        $dom->loadHTML("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8' /></head><body>$html</body></html>");    
        return $dom;
    }
    
    private function get_body_html($dom)
    {
        $html = $dom->saveHTML();
        
        $end = strrpos($html, "</body>");
        $start = strpos($html, "<body>") + 6;
        
        return substr($html, $start, $end - $start);    
    }
    
    function inflate($html)
    {
        $dom = $this->get_dom($html);
        
        $body = $dom->getElementsByTagName('body')->item(0);
        
        $this->_inflate($body);
        
        return $this->get_body_html($dom);    
    }    

    private function _inflate($node)
    {
        $attributes = $node->attributes;
        
        if ($attributes->length)
        {
            $id = $node->getAttribute('id');
            $node->removeAttribute('id');
            
            $old_attrs = $this->stored_attributes[$id];
            if ($old_attrs)
            {
                foreach ($old_attrs as $name => $value)
                {
                    $node->setAttribute($name, $value);
                }
            }
        }
        
        $childNodes = $node->childNodes;
        if ($childNodes)
        {
            $numChildren = $childNodes->length;
            for ($i = 0; $i < $numChildren; $i++)
            {
                $child = $childNodes->item($i);
                $this->_inflate($child);
            }
        }    
    }
    
    private function _deflate($node, $pre = false)
    {
        $name = $node->nodeName;
        
        if ($name == 'pre')
        {
            $pre = true;
        }
        
        $attributes = $node->attributes;
        
        if (!$pre && $node instanceof DOMText)
        {
            $node->data = trim(preg_replace('#\s+#', ' ', $node->data));
        }    
        if ($attributes->length)
        {
            $attr_map = array();
            $old_attrs = array();
            
            for ($j = 0; $j < $attributes->length; $j++)
            {
                $attr = $attributes->item($j);
                $old_attrs[] = $attr;
                $attr_map[$attr->name] = $attr->value;
            }
            
            foreach ($old_attrs as $old_attr)
            {
                $node->removeAttributeNode($old_attr);
            }

            $id = "{$this->next_id}";
            
            $this->stored_attributes[$id] = $attr_map;            
            $node->setAttribute("id", $id);
            $this->next_id++;
        }
        
        $childNodes = $node->childNodes;
        if ($childNodes)
        {
            $numChildren = $childNodes->length;
            for ($i = 0; $i < $numChildren; $i++)
            {
                $child = $childNodes->item($i);
                $this->_deflate($child, $pre);
            }
        }
    }
}
