<?php
    $message = $vars['message'];
    $topic = $vars['topic'];
    $offset = (int)$vars['offset'];
    
    echo view_entity($message);

    if ($message->is_enabled()) 
    {
        echo "<div style='font-size:10px'>";
        
        echo "<a href='{$topic->get_url()}/add_message?offset={$offset}&reply_to={$message->guid}#add_message'>";
        echo __('discussions:reply');
        echo "</a>";
        
        if ($message->can_edit())
        {        
            echo " <span class='admin_links'>";
            echo view('input/post_link', array(
                'href' => "{$message->get_container_entity()->get_url()}/delete_message?guid={$message->guid}",
                'text' => __('delete'),
                'confirm' => __('discussions:confirm_remove_message')
            ));
            echo "</span>";
        }
        echo "</div>";
    }        
