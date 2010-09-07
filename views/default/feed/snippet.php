<?php
    $thumbnail_url = $vars['thumbnail_url'];
    $link_url = $vars['link_url'];
    $mode = $vars['mode'];
    $title = $vars['title'];
    $org = $vars['org'];

    if ($thumbnail_url)
    {
        echo "<a class='feed_image_link' href='$link_url'><img src='$thumbnail_url' /></a>";
    }

    echo "<div style='padding-bottom:5px'>";
    echo sprintf($vars['heading_format'],
            $mode == 'self' ? escape($org->name) : "<a class='feed_org_name' href='{$org->get_url()}'>".escape($org->name)."</a>",
            "<a href='$link_url'>".escape($title)."</a>"
        );
    echo "</div>";

    $maxLength = 350;
    $content = $vars['content'];

    echo "<div class='feed_snippet'>";
    echo Markup::get_snippet($content, $maxLength);

    if (strlen($content) > $maxLength)
    {
        echo " <a class='feed_more' href='$link_url'>".__('feed:more')."</a>";
    }
    echo "</div>";  