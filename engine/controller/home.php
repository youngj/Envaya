<?php

class Controller_Home extends Controller
{
    function action_index()
    {
        set_context('home');
        add_generic_footer();
        $area = elgg_view("home");
        $title = elgg_echo("home:title");
        page_set_translatable(false);
        $body = elgg_view_layout('one_column', elgg_echo('home:heading'), $area);
        $this->page_draw($title, $body);
    }
}