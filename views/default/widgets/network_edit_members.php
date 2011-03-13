<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $offset = (int) get_input('offset');
    $limit = 5;
    $query = $org->query_network_members()->limit($limit, $offset);
    
    $count = $query->count();
    $members = $query->filter();
    
    if (!$count)
    {
        echo "<div>".__("network:no_members")."</div>";
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
        
        foreach ($members as $member)
        {
            echo "<div style='$border_top'>";
            echo "<div style='float:right;padding-top:10px;padding-right:10px'>";
            echo view('output/confirmlink', array(
                'class' => 'gridDelete',
                'confirm' => sprintf(__('network:confirm_delete_member'), $member->get_title()),
                'href' => "{$widget->get_edit_url()}/?action=delete_member&member_guid={$member->guid}",                
                'is_action' => true,
            ));
            
            echo "</div>";
            echo view_entity($member, array('showDetails' => true));
            echo "</div>";
            $border_top = "border-top:1px solid #f0f0f0";
        }
        
        echo $nav;
    }
    
    echo "<div style='text-align:center;padding-top:5px'><a href='{$widget->get_edit_url()}?action=add_member'><strong>".__('network:add_member')."</strong></a></div>";
    