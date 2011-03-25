<?php
    $widget = $vars['widget'];
    $topic = $vars['topic'];
        
    echo "<div>";
    
    echo "<div style='float:right;'>";
    
    echo "<a href='{$topic->get_edit_url()}?from=".escape(urlencode($_SERVER['REQUEST_URI']))."'>".__('edit')."</a> &middot; ";
       
    echo view('output/confirmlink', array(
            'text' => __('delete'),
            'confirm' => sprintf(__('discussions:confirm_remove_topic'), $list->address),
            'href' => "{$topic->get_url()}/edit?delete=1",
            'is_action' => true,
    ));
    
    echo "</div>";
        
    echo view_entity($topic);
            
    echo "</div>";