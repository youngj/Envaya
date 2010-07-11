<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->getUserEntity();
    $orgUrl = $org->getURL();

    $update = $item->getSubjectEntity();
    $url = $update->getURL();


    if ($update->hasImage())
    {
        echo "<a class='smallBlogImageLink' style='float:right' href='$url'><img src='{$update->getImageURL('small')}' /></a>";
    }

    if ($mode != 'self')
    {
        echo "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>: ";
    }

    /*
    $maxLength = 300;

    $content = translate_field($update,'content');

    echo elgg_view('output/longtext',
        array('value' => get_snippet($content, $maxLength))
    );

    if (strlen($content) > $maxLength)
    {
        echo " <a class='feed_more' href='$url'>".elgg_echo('feed:more')."</a>";
    }
    */

    echo $update->renderContent();
