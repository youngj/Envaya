<?php
    $options = $vars['options'];
    
    $values = $vars['value'];    
    if ($values)
    {    
        if (!is_array($values))
        {
            $values = array($values);
        }
        foreach ($values as $value)
        {
            $text = escape(@$options[$value] ?: $value);
            echo "<div class='checkbox_value'>$text</div> ";
        }    
    }
