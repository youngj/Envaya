<?php
    $org = $vars['org'];
    $widget = $vars['home_widget'];

    $url = $widget->get_url();
    
    foreach ($widget->query_menu_widgets()->limit(5)->filter() as $sub_widget)
    {
        $feed_html = $sub_widget->render_view_feed();        
        if ($feed_html)
        {
            echo view('feed/snippet', array(
                'content' => "<em>".escape($sub_widget->get_title())."</em>: $feed_html",
                'max_length' => 500,
                'link_url' => $url,
            ));
        }
    }