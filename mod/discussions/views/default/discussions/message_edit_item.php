<?php
    $message = $vars['message'];
    $topic = $vars['topic'];
        
    echo "<div>";
    
    echo "<div style='float:right;'>";
       
    echo "<a href='{$message->get_base_url()}/edit'>".__('edit')."</a> &middot; ";
       
    echo view('input/post_link', array(
            'text' => __('delete'),
            'confirm' => __('discussions:confirm_remove_message'),
            'href' => "{$message->get_base_url()}/delete",
    ));
    
    echo "</div>";
        
    echo view('discussions/message', array('message' => $message));
            
    echo "</div>";