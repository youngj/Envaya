<div class='padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $org->getPartnerships($limit, $offset, true);
    $entities = $org->getPartnerships($limit, $offset);
    
    echo elgg_view_entity_list($entities, $count, $offset, $limit, false, false, $pagination = true);
        
    if (!$count)
    {
        echo "<div class='padded'>".elgg_echo("partner:none")."</div>";
    }
?>
</div>