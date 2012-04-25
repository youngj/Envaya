<?php

class Query_Filter_User_Fulltext extends Query_Filter
{    
    static function get_name()
    {
        return "Name";
    }
    
    static function get_param_name()
    {
        return 'q';
    }
    
    function _apply($query)
    {
        return $query->fulltext($this->value);
    }        
    
    function render_input($vars)
    {
        if (isset($vars['onchange']))
        {
            $vars['attrs']['onchange'] = $vars['onchange'];            
            unset($vars['onchange']);
        }
        
        if (!isset($vars['style']))
        {
            $vars['style'] = '';
        }
        $vars['style'] .= ';width:200px';
                
        return view('input/text', array_merge($vars, array(       
            'value' => $this->value,        
        )));
    }    
    
    function render_view()
    {
        return escape($this->value);
    }   
    
}