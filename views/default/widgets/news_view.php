<div class='section_content'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $end_guid = $vars['end_guid'];
    
    if (get_input('offset') === '' && $end_guid)
    {
        $offset = $org->query_news_updates()->where('u.guid > ?', $end_guid)->count();
    }
    else
    {
        $offset = (int) get_input('offset');
    }

    $limit = 10;
    
    $query = $org->query_news_updates();    
    $count = $query->count();
    $entities = $query->limit($limit, $offset)->filter();            
?>

<?php if (!empty($entities)) { ?>

<div style='clear:both'></div>
<?php } ?>
<?php

    echo view('paged_list', array(
        'entities' => $entities,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
    ));        
    
    if (!$count)
    {
        echo "<div class='padded'>".__("widget:news:empty")."</div>";
    }

?>
</div>