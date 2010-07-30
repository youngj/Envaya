<?php

class Controller_Page extends Controller
{
    function action_view()
    {
        add_generic_footer();
        $pageName = $this->request->param('name');

        if (preg_match('/[^\w]/', $pageName))
        {
            not_found();
        }
        else
        {
            forward("envaya/$pageName");
        }
    }
}