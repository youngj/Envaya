<?php

/*
 * Controller for the main home page
 *
 * URL: / 
 *      /home
 */
class Controller_EnvayaHome extends Controller
{
    static $routes = array(
        array('action' => 'action_index')
    );

    function action_index()
    {
        Permission_Public::require_any();        
    
        $this->allow_content_translation();
        $this->page_draw_vars['hide_translate_bar'] = true;

        $footer = PageContext::get_submenu('footer');
    
        $footer->add_link(__('about'), "/envaya");
        $footer->add_link(__('contact'), "/envaya/contact");
        $footer->add_link(__('donate'), "/envaya/page/contribute");    
        
        $this->allow_view_types(null);
        
        $this->page_draw(array(
            'theme' => 'Theme_Home',
            'title' => __('home:title'),
            'header' => '',
            'content' => view('home/home')        
        ));
    }
}