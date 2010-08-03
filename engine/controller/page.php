<?php

class Controller_Page extends Controller
{
    function action_view()
    {
        $this->add_generic_footer();
        $pageName = $this->request->param('name');

        if (preg_match('/[^\w]/', $pageName))
        {
            not_found();
        }
        else if ($pageName == 'about')
        {
            forward("/envaya");
        }
        else if ($pageName == 'donate')
        {
            forward("/envaya/contribute");
        }
        else
        {
            forward("/envaya/$pageName");
        }
    }
}