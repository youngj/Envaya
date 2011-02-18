<?php
    $value = @$vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $name = $vars['name'];
    
    echo view("input/longtext", array(
        'name' => $name,
        'value' => $valueIsHTML ? $value : view('output/longtext', array('value' => $value))));
?>