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
            $area = elgg_view("page/$pageName");
            if (!$area)
            {
                not_found();
            }
            else
            {
                $title = elgg_echo("$pageName:title");
                $args = array('org_only' => (in_array($pageName, array('why'))));

                $body = elgg_view_layout('one_column_padded', elgg_view_title($title, $args), $area);
                $this->page_draw($title, $body);
            }
        }
    }
}