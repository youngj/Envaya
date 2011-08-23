<?php    
    if (@$vars['design']['custom_header'])
    {
        echo view('page_elements/site_image_header', $vars);
    }
    else
    {
        echo view('page_elements/site_default_header', $vars);
    }