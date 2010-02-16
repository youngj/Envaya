<?php
    /**
     * CSRF security token view for use with secure forms.
     * 
     * It is still recommended that you use input/form.
     * 
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */
     
     $lang = $vars['value'];
     if (empty($lang))
     {
        $user = get_loggedin_user();
        if ($user)
        {
            $lang = $user->language;
        }    
     }     
     
    echo elgg_view('input/pulldown', array('internalname' => $vars['internalname'], 'value' => $lang,
        'options_values' => get_installed_translations()
    ));
?>
