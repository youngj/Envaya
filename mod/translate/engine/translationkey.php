<?php

class TranslationKey extends Entity
{
    static $table_name = 'translation_keys';
    static $table_attributes = array(
        'subtype_id' => '',
        'name' => '',
        'language_guid' => 0,
        'num_translations' => 0,
        'best_translation' => '',
        'best_translation_guid' => 0,
    );    
    
    function update($recursive = false)
    {
        $this->num_translations = $this->query_translations()->count();

        $best = $this->query_translations()
            ->where('score >= 0')
            ->order_by('time_created desc')
            ->get();
            
        if ($best)
        {
            $this->best_translation = $best->value;
            $this->best_translation_guid = $best->guid;
        }
        else
        {
            $this->best_translation = '';
            $this->best_translation_guid = 0;
        }
        $this->save();
    }
    
    function get_language()
    {
        return TranslationLanguage::get_by_guid($this->language_guid);
    }
    
    public function new_translation()
    {
        $translation = new Translation();   
        $translation->container_guid = $this->guid;
        return $translation;
    }
    
    function get_title()
    {
        return $this->name;
    }
        
    function get_best_translation()
    {
        return Translation::get_by_guid($this->best_translation_guid);
    }
        
    function query_translations()
    {
        return Translation::query()->where('container_guid = ?', $this->guid);
    }

    function query_comments()
    {
        return TranslationKeyComment::query()
            ->where('container_guid = ? OR (key_name = ? AND language_guid = 0)', $this->guid, $this->name);
    }
    
    function get_value_in_lang($lang)
    {
        throw new NotImplementedException(); 
    }
    
    function get_default_value()
    {
        return $this->get_value_in_lang(null);
    }
    
    function view_input($initial_value)
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
            'value' => $initial_value,
            'track_dirty' => true,
        ));     
    }
    
    function view_value($value, $snippet_len = null)
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
        
    function get_placeholders()
    {
        return array();
    }
    
    function sanitize_value($value)
    {
        return $value;
    }

    function get_current_base_value()
    {
        return $this->get_default_value();
    }        
    
    function get_current_base_lang()
    {
        return Language::get_current_code();
    }
}