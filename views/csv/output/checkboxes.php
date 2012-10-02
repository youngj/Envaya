<?php
    $options = $vars['options'];    
    $values = $vars['value'];    
    
    if ($values)
    {
        if (!is_array($values))
        {
            $values = array($values);
        }
        
        $res = array();        
        foreach ($values as $value)
        {
            $res[] = @$options[$value] ?: $value;
        }    
        echo implode(",", $res);
    }