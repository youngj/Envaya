<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->get_user_entity();
    $orgUrl = $org->get_url();

    $partner = $item->get_subject_entity();
    $partnerUrl = $partner->get_url();

    echo sprintf(__('feed:partnership'),
        $mode == 'self' ? escape($org->name) :"<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>",
        "<a class='feed_org_name' href='$partnerUrl'>".escape($partner->name)."</a>"
    );

