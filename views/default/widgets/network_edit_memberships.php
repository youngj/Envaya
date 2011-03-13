<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $offset = (int) get_input('offset2');
    $limit = 5;
    $query = $org->query_network_memberships()->limit($limit, $offset);
    $count = $query->count();
    
    $memberships = $query->filter();
    
    if (!$count)
    {
        echo "<div>".__("network:no_memberships")."</div>";
    }
    else
    {
        $nav = view('navigation/pagination',array(
            'offset' => $offset,
            'word' => 'offset2',
            'count' => $count,
            'limit' => $limit,
        ));    
        
        echo $nav;
        
        $border_top = '';
        
        foreach ($memberships as $membership)
        {
            $network = $membership->get_container_entity();
            echo "<div style='$border_top'>";
            echo "<div style='float:right;padding-top:10px;padding-right:10px'>";
            echo view('output/confirmlink', array(
                'class' => 'gridDelete',
                'confirm' => sprintf(__('network:confirm_delete_membership'), $network->get_title()),
                'href' => "{$widget->get_edit_url()}/?action=delete_membership&member_guid={$member->guid}",                
                'is_action' => true,
            ));
            
            echo "</div>";
            echo view_entity($network);
            echo "</div>";
            $border_top = "border-top:1px solid #f0f0f0";
        }
        
        echo $nav;
    
    }    
    
    echo "<div style='text-align:center;padding-top:5px'><a href='{$widget->get_edit_url()}?action=add_membership'><strong>".__('network:add_membership')."</strong></a></div>";    