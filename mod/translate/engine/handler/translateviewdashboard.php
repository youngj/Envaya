<?php

class Handler_TranslateViewDashboard
{
    static function execute($vars) 
    {           
        $user = $vars['user'];
        if (!($user instanceof Organization)) // hmm...
        {        
            $links = $vars['dashboard_links_menu'];
            $links->add_item(view('account/link_item', array(
                'href' => '/tr', 
                'style' => 'background:url(/_media/images/translate/world.gif) no-repeat 4px 7px;', 
                'text' => __('itrans:translations')
            )));
        }
    }
}