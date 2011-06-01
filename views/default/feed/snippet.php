<?php   
    $link_url = null;
    $content = null;
    $max_length = 350;
    extract($vars);

    echo "<div class='feed_snippet'>";
    echo Markup::get_snippet($content, $max_length);

    if (strlen($content) > $max_length)
    {
        echo " <a class='feed_more' href='$link_url'>".__('feed:more')."</a>";
    }
    echo "</div>";  