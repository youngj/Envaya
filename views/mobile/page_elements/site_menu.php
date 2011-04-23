<?php    
    if (@$vars['is_site_home'])
    {
        echo view('page_elements/site_menu', $vars, 'default');
    }