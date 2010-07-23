<?php

class Controller_Home extends Controller
{
    function action_index()
    {
        set_context('home');
        add_generic_footer();
        $area = elgg_view("home");
        $title = __("home:title");
        page_set_translatable(false);
        $body = elgg_view_layout('one_column', __('home:heading'), $area);
        $this->page_draw($title, $body);
    }
}