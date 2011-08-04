<?php

/*
 * A Mixin for the TranslationKey class that defines the behavior for the translation interface 
 * for the current translation key. Each TranslationKey instance may be associated with a different
 * subclass of TranslationKeyBehavior in the translationkeybehavior/ directory.
 */
class TranslationKeyBehavior extends Mixin
{
    public function sanitize_value($value)
    {
        return $value;
    }
    
    public function view_value($value, $snippet_len = null)
    {
        if ($snippet_len != null)
        {
            $v = Markup::truncate_at_word_boundary($value, $snippet_len);
            if ($v != $value)
            {
                $value = $v . "...";
            }
        }
    
        if (strpos($this->get_default_value(), "\n") !== false)
        {
            $view_name = 'output/longtext';            
        }
        else
        {   
            $view_name = 'output/text';            
        }
        
        $res = view($view_name, array('value' => $value));
        
        if ($snippet_len == null)
        {
            $res = "<div style='width:350px;border:1px solid #ccc;padding:4px'>$res</div>";
        }
        
        return $res;    
    }
    
    public function view_input($value)
    {
        $base_value = $this->get_default_value();
    
        $style = 'width:350px;margin-top:0px';
    
        if (strlen($base_value) > 45 || strpos($base_value, "\n") !== FALSE)
        {
           $view = "input/longtext";
           $style .= ";height:".(25+floor(strlen($base_value)/45)*30)."px";
        }
        else
        {
            $view = "input/text";
        }                 

        echo view($view, array(
            'name' => 'value',
            'style' => $style,
            'value' => $value,
            'track_dirty' => true,
        ));         
    }
}