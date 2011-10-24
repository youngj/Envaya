<?php
    $org = $vars['org'];
    $widget = $vars['widget'];    
    $type = $vars['type'];
    
    echo "<a href='{$widget->get_edit_url()}?action=add_relationship&type={$type}&org_guid={$org->guid}'>"
        .Relationship::msg_for_type($type, 'add_this_link')."</a>";