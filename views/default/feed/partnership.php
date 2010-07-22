<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->getUserEntity();
    $orgUrl = $org->getURL();

    $partner = $item->getSubjectEntity();
    $partnerUrl = $partner->getURL();

    echo sprintf(__('feed:partnership'),
        $mode == 'self' ? escape($org->name) :"<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>",
        "<a class='feed_org_name' href='$partnerUrl'>".escape($partner->name)."</a>"
    );

