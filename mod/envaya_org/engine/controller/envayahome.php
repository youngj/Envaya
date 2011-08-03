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
        $this->prefer_http();    
        $this->allow_content_translation();
        $this->page_draw_vars['hide_translate_bar'] = true;

        $footer = PageContext::get_submenu('footer');
    
        $footer->add_item(__('about'), "/envaya");
        $footer->add_item(__('contact'), "/envaya/contact");
        $footer->add_item(__('donate'), "/envaya/page/contribute");    
        
        $this->allow_view_types(null);
        
        $this->page_draw(array(
            'theme_name' => 'home',
            'title' => __('home:title'),
            'header' => '',
            'content' => view('home/home')        
        ));
    }
}