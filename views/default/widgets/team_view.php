<div class='padded'>
<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $teamMembers = $org->getTeamMembers();
    
    if (!empty($teamMembers))
    {    
        foreach ($teamMembers as $teamMember)
        {
            echo elgg_view_entity($teamMember);
        }
    }
    else
    {
        echo elgg_echo("widget:team:empty");
    }
?>
</div>