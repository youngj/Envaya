<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->getUserEntity();
    $orgUrl = $org->getURL();

    $widget = $item->getSubjectEntity();
    $widgetUrl = $widget->getURL();

    if ($widget->hasImage())
    {
        echo "<a class='feed_image_link' href='$widgetUrl'><img src='{$widget->thumbnail_url}' /></a>";
    }

    echo "<div style='padding-bottom:5px'>";
    echo sprintf(__('feed:new_widget'),
        $mode == 'self' ? escape($org->name) : "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>",
        "<a href='$widgetUrl'>{$widget->getTitle()}</a>"
    );
    echo "</div>";

    $maxLength = 300;

    $content = $widget->renderContent();

    echo "<div class='feed_snippet'>";
    echo get_snippet($content, $maxLength);

    if (strlen($content) > $maxLength)
    {
        echo " <a class='feed_more' href='$widgetUrl'>".__('feed:more')."</a>";
    }
    echo "</div>";