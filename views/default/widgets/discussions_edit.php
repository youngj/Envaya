<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
         
    ob_start();           
        
    /*
    echo "<a href='{$widget->get_edit_url()}?action=add_list'>".__('discussions:add_list')."</a><br />";    
    
    if (!$org->email)
    {    
        echo __('discussions:email_required');
    }
    else
    {
        echo "<a href='{$widget->get_edit_url()}?action=create_list'>".__('discussions:create_list')."</a>";
    }
    */

    echo view('widgets/discussions_edit_topics', array('widget' => $widget));   
        
    /*
    echo view('widgets/discussions_edit_memberships', array('widget' => $widget));
    */
        
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
       'widget' => $widget,
       'body' => $content,
       'noSave' => true          
    ));
