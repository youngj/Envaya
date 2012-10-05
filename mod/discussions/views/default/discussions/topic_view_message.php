<?php
    $message = $vars['message'];
    $topic = $vars['topic'];
    $offset = (int)$vars['offset'];
    
    echo view('discussions/message', array('message' => $message));

    if ($message->status == DiscussionMessage::Published) 
    {
        echo "<div style='font-size:10px'>";
        
        echo "<a href='{$topic->get_url()}/add_message?offset={$offset}&reply_to={$message->guid}#add_message'>";
        echo __('discussions:reply');
        echo "</a>";
        
        if (Permission_EditDiscussionMessage::has_for_entity($message))
        {      
            echo " <a href='{$message->get_base_url()}/edit'>".__('edit')."</a> ";
        
            echo " <span class='admin_links'>";
            echo view('input/post_link', array(
                'href' => "{$message->get_base_url()}/delete",
                'text' => __('delete'),
                'confirm' => __('discussions:confirm_remove_message')
            ));
            echo "</span>";
        }
        echo "</div>";
    }        
