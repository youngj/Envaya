<?php

class Controller_Home extends Controller
{
    function action_index()
    {       
        PageContext::set_theme('home');
        $this->add_generic_footer();
        $area = view("home");
        $title = __("home:title");
        PageContext::set_translatable(false);
        $body = view_layout('one_column', '', $area);
        $this->page_draw($title, $body);
    }
}