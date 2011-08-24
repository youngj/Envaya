<?php

abstract class Query_Filter_Select extends Query_Filter
{    
    function get_options()
    {
        return array();
    }

    static function get_empty_option()
    {
        return static::get_all_text();
    }

    function render_view()
    {
        if ($this->is_empty_value())
        {
            return $this->get_all_text();
        }
        else
        {    
            $options = $this->get_options();
            return @$options[$this->value];
        }
    }   
   
    function render_input($vars)
    {
        if (isset($vars['onchange']))
        {
            $vars['attrs']['onchange'] = $vars['onchange'];
            $vars['attrs']['onkeypress'] = $vars['onchange'];
            unset($vars['onchange']);
        }
                
        if (isset($vars['empty_option']) && $vars['empty_option'] === false)
        {
            unset($vars['empty_option']);
        }
        else
        {
            $vars['empty_option'] = $this->get_empty_option();
        }
                
        return view('input/pulldown', array_merge($vars, array(                               
            'options' => $this->get_options(),
            'value' => $this->value,        
        )));
    }
}