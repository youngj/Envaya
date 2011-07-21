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
    
    public function view_content_value($value)
    {
        return $value;
    }
    
    public function view_content_input($value)
    {
        return view('input/tinymce', array('name' => 'value', 'value' => $value));
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