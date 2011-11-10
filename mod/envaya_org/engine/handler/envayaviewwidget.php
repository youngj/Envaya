<?php

class Handler_EnvayaViewWidget
{
    static function execute($vars) {
    
        $user = $vars['user'];
    
        $vars['page_draw_args']['show_next_steps'] = $vars['user']->equals(Session::get_logged_in_user());
        
        if (($user instanceof Organization) && Permission_EditMainSite::has_for_root())
        {
            $user_actions_menu = $vars['user_actions_menu'];
        
            $user_actions_menu->add_link(
                __('featured:add'),
                "/admin/envaya/add_featured?username={$user->username}"
            );
            
            $user_actions_menu->add_item(view('admin/featured_photo_link', array('user' => $user)));
        }
        
        return $vars;
    }
}