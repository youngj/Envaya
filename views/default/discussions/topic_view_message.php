<?php
    $message = $vars['message'];
    
    echo view_entity($message);

    if ($message->can_edit() && $message->status == EntityStatus::Enabled) 
    {
        echo "<div class='admin_links'>";
        echo view('output/confirmlink', array(
            'href' => "{$message->get_container_entity()->get_url()}/delete_message?guid={$message->guid}",
            'text' => __('delete'),
            'confirm' => __('discussions:confirm_remove_message')
        ));
        echo "</div>";
    }        
