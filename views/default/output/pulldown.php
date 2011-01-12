<?php
    $options = $vars['options'];
    
    $value = @$vars['value'];
    if ($value !== null && $value !== '')
    {    
        echo escape(@$options[$value] ?: $value);
    }    
    else
    {
        echo escape(@$vars['empty_option']);
    }