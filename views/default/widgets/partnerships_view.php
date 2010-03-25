<?php 
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $partnerships = Partnership::getPartnerships($org->guid);
    
    if(!$partnerships)
    {
        echo elgg_echo("org:noPartnerships");
    }
    else
    {
        foreach($partnerships as $p)
        {
            $partnerOrgEntity = get_entity($p->org_guid);
            echo "<img style='float:left;' src='" . $partnerOrgEntity->getIcon('tiny') . "' />";
            echo "<a href='{$partnerOrgEntity->getUrl()}'>{$partnerOrgEntity->name}</a>";
            echo "<div style='clear:both;' />{$p->description}";
            echo "<br><br>";            
        }
    }
?>

