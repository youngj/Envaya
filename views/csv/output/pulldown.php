<?php
    $options = $vars['options'];    
    $value = @$vars['value'];
    if ($value !== null && $value !== '')
    {    
        echo @$options[$value] ?: $value;
    }    
