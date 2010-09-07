<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->get_user_entity();
    $orgUrl = $org->get_url();
    
    echo "<div style='padding-bottom:5px'>";
    echo sprintf(__('feed:edit_home'),
        $mode == 'self' ? escape($org->name) : "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>");
    echo "</div>";

    if ($mode != 'self')
    {
        echo view('feed/home', array('org' => $org, 'home_widget' => $org->get_widget_by_name('home')));
    }
