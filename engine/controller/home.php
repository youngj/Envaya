<?php

class Controller_Home extends Controller
{
    function action_index()
    {       
        $this->require_http();    
        $this->add_generic_footer();        
        
        PageContext::set_translatable(false);

        $this->page_draw(array(
            'theme_name' => 'home',
            'title' => __('home:title'),
            'header' => '',
            'content' => view('home')        
        ));
    }
}