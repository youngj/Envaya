<?php
    if (@$vars['css_url'])
    {
        echo "<link rel='stylesheet' href='".escape($vars['css_url'])."' type='text/css' />";
    }       
    
    if (isset($vars['design']))
    {    
        $theme = $vars['theme'];
        
        $css = $theme::render_custom_css(@$vars['design']['theme_options']);

        if ($css)
        {
            echo "<style type='text/css'>\n$css</style>";
        }
    }    