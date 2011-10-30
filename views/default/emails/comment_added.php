<?php
    $comment = $vars['comment'];
    
    $widget = $comment->get_container_entity();
    $comments_url = secure_url($widget->get_url()."?comments=1#comments");

    echo $comment->render_content();
    echo "<div><a href='$comments_url'>$comments_url</a></div>";
