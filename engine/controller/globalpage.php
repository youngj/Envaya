<?php

class Controller_GlobalPage extends Controller
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
            forward("/envaya/page/contribute");
        }
        else
        {
            forward("/envaya/page/$pageName");
        }
    }
}