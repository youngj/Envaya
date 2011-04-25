<?php

/*
 * Controller for the main home page
 *
 * URL: / 
 *      /home
 */
class Controller_Home extends Controller
{
    static $routes = array(
        array()
    );      

    function action_index()
    {       
        $this->prefer_http();    
        $this->add_generic_footer();
        $this->allow_view_types(null);
        
        $this->page_draw(array(
            'theme_name' => 'home',
            'title' => __('home:title'),
            'header' => '',
            'content' => view('home/home')        
        ));
    }
}