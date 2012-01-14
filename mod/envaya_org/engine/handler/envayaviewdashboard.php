<?php

class Handler_EnvayaViewDashboard
{
    static function execute($vars)
    {   
        if (Permission_EditMainSite::has_for_root())
        {        
            $links = $vars['dashboard_links_menu'];
            
            $links->add_item(view('account/link_item', array(
                'href' => '/admin/envaya/home_page', 
                'text' => 'Configure Home Page',
                'class' => 'icon_admin'
            )));
        }            
    }
}
