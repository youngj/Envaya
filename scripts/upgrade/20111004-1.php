<?php

    require_once "start.php";         
    
    foreach (Comment::query()
        ->where('time_created < ?', 1317773178)
        ->filter() as $comment)
    {
        $html_content = nl2br(escape($comment->content));
        
        if ($html_content == $comment->content)
            continue;
    
        if ($comment->get_metadata('is_html'))
            continue;

        error_log("{$comment->guid}");
        $comment->set_metadata('is_html', true);
        $comment->content = $html_content;
        $comment->save();
    }