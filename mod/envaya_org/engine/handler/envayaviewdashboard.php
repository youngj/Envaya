<?php

class Handler_EnvayaViewDashboard
{
    static function execute($vars)
    {   
        if (Permission_EditMainSite::has_for_root())
        {        
            $links = $vars['dashboard_links_menu'];
            
            $links->add_item(view('account/link_item', array(
                'href' => '/org/featured', 
                'text' => 'Featured Organizations',
                'class' => 'icon_admin'
            )));
                   
            $links->add_item(view('account/link_item', array(
                'href' => '/admin/envaya/featured_photos', 
                'text' => 'Featured Photos',
                'class' => 'icon_admin'
            )));                
        }            
    }
}
