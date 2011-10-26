<?php
    $comment = $vars['entity'];    
    
    echo "<div class='comment' id='comment{$comment->guid}'>";
    
    if ($comment->is_enabled())
    {
        echo "<div class='comment_name'>";            
        $owner = $comment->get_owner_entity(); 
        
        $nameHTML = escape($comment->get_name());
        
        if ($owner && ($owner instanceof Organization))
        {    
            $nameHTML = "<a href='{$owner->get_url()}'>$nameHTML</a>";
        }
        
        if ($comment->location)
        {
            $nameHTML = "$nameHTML (".escape($comment->location).")";
        }
        
        echo sprintf(__('comment:name_said'), $nameHTML);    
        echo "</div>";
        
		echo $comment->render_content();
        
        echo "<div class='blog_date'>";
        echo $comment->get_date_text();
        
        if ($comment->time_updated > $comment->time_created)
        {
            echo " ".strtr(__('date:edited'), array(
                '{date}' => $comment->get_date_text($comment->time_updated)
            ));
        }
        
        echo "</div>";
        
        if ($comment->can_edit()) 
        {
            echo "<div style='font-size:10px'>";
            echo "<a href='{$comment->get_base_url()}/edit'>".__('edit')."</a>";        
            echo " <span class='admin_links'>";
            echo view('input/post_link', array(
                'href' => "{$comment->get_base_url()}/delete",
                'text' => __('delete'),
                'confirm' => __('comment:confirm_delete')
            ));
            echo "</span>";
            echo "</div>";
        }    
    }
    else
    {
        echo "<div class='comment_deleted'>[".__('comment:deleted_marker')."]</div>";
    }
    echo "</div>";