<?php

class Mixin_Content extends Mixin
{    
    static function mixin_table_attributes()
    {
        return array(
            'content' => '',
            'data_types' => 0,
            'language' => '',
        );
    }
    
    public function set_content($content, $isSanitized = false)
    {
        if (!$isSanitized)
        {
            $content = Markup::sanitize_html($content);
        }
        
        $this->content = $content;
        $this->set_data_type(DataType::HTML, true);

        $thumbnailUrl = UploadedFile::get_thumbnail_url_from_html($content);        
        $this->set_data_type(DataType::Image, $thumbnailUrl != null);
        $this->thumbnail_url = $thumbnailUrl;            

        if (!$this->language)
        {            
            $this->language = GoogleTranslate::guess_language($this->content);
        }
    }

    public function render_content($markup_mode = null)
    {
        $isHTML = $this->has_data_type(DataType::HTML);

        $content = $this->translate_field('content', $isHTML);

        if ($isHTML)
        {
            $content = Markup::render_custom_tags($content, $markup_mode);
        
            return $content; // html content should be sanitized when it is input!
        }
        else
        {
            return view('output/longtext', array('value' => $content));
        }
    }

    public function has_data_type($dataType)
    {
        return ($this->data_types & $dataType) != 0;
    }

    public function set_data_type($dataType, $val)
    {
        if ($val)
        {
            $this->data_types |= $dataType;
        }
        else
        {
            $this->data_types &= ~$dataType;
        }
    }        
}