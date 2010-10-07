<?php   
    $maxLength = @$vars['max_length'] ?: 350;
    $link_url = $vars['link_url'];
    $content = $vars['content'];

    echo "<div class='feed_snippet'>";
    echo Markup::get_snippet($content, $maxLength);

    if (strlen($content) > $maxLength)
    {
        echo " <a class='feed_more' href='$link_url'>".__('feed:more')."</a>";
    }
    echo "</div>";  