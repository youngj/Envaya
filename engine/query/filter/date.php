<?php

abstract class Query_Filter_Date extends Query_Filter
{    
    function _apply($query)
    {
        if (!$this->value)
        {
            return $query;
        }
        else
        {
            $time = strtotime($this->value);
            if ($time)
            {               
                return $this->apply_time($query, $time);
            }
            else
            {
                return $query->where('1=0');
            }
        }
    }        
    
    abstract function apply_time($query, $time);
    
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