<?php
    $options = $vars['options'];
    
    $values = $vars['value'];
    if ($values != '' && !is_array($values))
    {
        $values = array($values);
    }
    foreach ($values as $value)
    {
        $text = @$options[$value] ?: escape($value);
        echo "<div class='checkbox_value'>$text</div> ";
    }    
?>