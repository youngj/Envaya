<?php
    $value = @$vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $internalname = $vars['internalname'];
    
    echo view("input/longtext", array(
        'internalname' => $internalname,
        'value' => $valueIsHTML ? $value : view('output/longtext', array('value' => $value))));
?>