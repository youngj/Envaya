<?php       
     $lang = $vars['value'];
     if (empty($lang))
     {
        $lang = Language::get_current_code();
     }     
     
    echo view('input/pulldown', array('name' => $vars['name'], 'value' => $lang,
        'options' => Language::get_options()
    ));
?>
