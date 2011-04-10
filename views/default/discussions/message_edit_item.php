<?php
    $widget = $vars['widget'];
    $message = $vars['message'];
    $topic = $vars['topic'];
        
    echo "<div>";
    
    echo "<div style='float:right;'>";
           
    echo view('output/confirmlink', array(
            'text' => __('delete'),
            'confirm' => __('discussions:confirm_remove_message'),
            'href' => "{$topic->get_url()}/delete_message?guid={$message->guid}",
    ));
    
    echo "</div>";
        
    echo view_entity($message);
            
    echo "</div>";