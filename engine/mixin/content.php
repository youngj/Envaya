<?php

/*
 * Mixin for Entity classes that have free-text content stored as HTML.
 */
class Mixin_Content extends Mixin
{    
    static function mixin_table_attributes()
    {
        return array(
            'content' => '',
            'thumbnail_url' => '',
            'language' => '',
        );
    }
    
    public function sanitize_content_value($value)
    {
        return Markup::sanitize_html($value);
    }
    
    public function view_content_value($value, $snippet_len = null)
    {
        if ($snippet_len != null)
        {
            $value = preg_replace('/<img [^>]+>/', ' <strong>(image)</strong> ', $value);
            $value = preg_replace('/<scribd [^>]+>/', ' <strong>(document)</strong> ', $value);
        
            return Markup::get_snippet($value, $snippet_len);
        }
        else
        {
            $value = "<div style='width:470px;height:365px;padding:4px;border:1px solid #ccc;overflow:auto'>$value</div>";
            if (Session::isloggedin()) // hack to make it line up with tinymce content
            {
                $value = "<div style='padding-top:28px'>$value</div>";                
            }
            return $value;
        }
    }
    
    public function view_content_input($value)
    {
        return view('input/tinymce', array(
            'name' => 'value', 
            'value' => $value, 
            'height' => 396, 
            'width' => 470,
            'track_dirty' => true,
        ));
    }
    
    public function get_content_snippet($value, $maxLength = 100)
    {
        return Markup::get_snippet($value, $maxLength);
    }
    
    public function set_content($content, $isSanitized = false)
    {
        if (!$isSanitized)
        {
            $content = $this->sanitize_content_value($content);
        }
        
        $this->content = $content;
        $this->thumbnail_url = UploadedFile::get_thumbnail_url_from_html($content);        

        if (!$this->language)
        {            
            $this->language = GoogleTranslate::guess_language($this->content);
        }
    }

    public function render_content($markup_mode = null)
    {
        if ($markup_mode != Markup::Feed)
        {
            $content = $this->translate_field('content');
        }
        else
        {
            $content = $this->content;
        }

        // html content should be sanitized when it is input!        
        
        return Markup::render_custom_tags($content, $markup_mode);        
    }

    public function get_snippet($maxLength = 100)
    {
        return Markup::get_snippet($this->content, $maxLength);
    }       
}