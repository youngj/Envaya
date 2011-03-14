<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $relationshipType = $vars['type'];
    $deleteAction = $vars['delete_action'];
    $addAction = $vars['add_action'];
    $empty = $vars['empty_message'];
    $add = $vars['add_message'];
    $confirmDelete = $vars['confirm_delete_message'];
    $header = $vars['header'];
    
    $offset = (int) get_input('offset');
    $limit = 5;
    $query = $org->query_relationships()->where('`type` = ?', $relationshipType)->limit($limit, $offset);
    
    $count = $query->count();
    $relationships = $query->filter();
    
    ob_start();
    
    if (!$count)
    {
        echo "<div>$empty</div>";
    }
    else
    {
        $nav = view('navigation/pagination',array(
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
        ));    
        
        echo $nav;
        
        $border_top = '';
        
        foreach ($relationships as $relationship)
        {
            echo "<div style='$border_top'>";
            echo "<div style='float:right;padding-top:10px;padding-right:10px'>";
            echo view('output/confirmlink', array(
                'class' => 'gridDelete',
                'confirm' => sprintf($confirmDelete, $relationship->get_subject_name()),
                'href' => "{$widget->get_edit_url()}/?action={$deleteAction}&guid={$relationship->guid}",                
                'is_action' => true,
            ));                
            echo "</div>";
            echo view_entity($relationship);
            echo "</div>";
            $border_top = "border-top:1px solid #f0f0f0";
        }
        
        echo $nav;
    }
    
    echo "<div style='text-align:center;padding-top:5px'><a href='{$widget->get_edit_url()}?action={$addAction}'><strong>$add</strong></a></div>";
    
    $content = ob_get_clean();
    
    echo view('section', array('header' => $header, 'content' => $content));