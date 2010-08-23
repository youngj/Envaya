<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->get_user_entity();
    $orgUrl = $org->get_url();

    $widget = $item->get_subject_entity();
    $widgetUrl = rewrite_to_current_domain($widget->get_url());

    if ($widget->has_image())
    {
        echo "<a class='feed_image_link' href='$widgetUrl'><img src='{$widget->thumbnail_url}' /></a>";
    }

    echo "<div style='padding-bottom:5px'>";
    echo sprintf(__('feed:new_widget'),
        $mode == 'self' ? escape($org->name) : "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>",
        "<a href='$widgetUrl'>{$widget->get_title()}</a>"
    );
    echo "</div>";

    $maxLength = 300;

    $content = $widget->render_content();

    echo "<div class='feed_snippet'>";
    echo Markup::get_snippet($content, $maxLength);

    if (strlen($content) > $maxLength)
    {
        echo " <a class='feed_more' href='$widgetUrl'>".__('feed:more')."</a>";
    }
    echo "</div>";