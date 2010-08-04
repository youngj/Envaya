<?php
    
     
     $lang = $vars['value'];
     if (empty($lang))
     {
        $lang = get_language();    
     }     
     
    echo view('input/pulldown', array('internalname' => $vars['internalname'], 'value' => $lang,
        'options_values' => get_installed_translations()
    ));
?>
