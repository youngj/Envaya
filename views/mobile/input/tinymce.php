<?php
    $value = @$vars['value'];
    $valueIsHTML = isset($vars['valueIsHTML']) ? $vars['valueIsHTML'] : true;
    $internalname = $vars['internalname'];
    global $CONFIG;
    
    echo view("input/longtext", array(
        'internalname' => $internalname,
        'value' => $valueIsHTML ? $value : view('output/longtext', array('value' => $value))));
?>