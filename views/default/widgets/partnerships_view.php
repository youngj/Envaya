<div class='section_content padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $offset = (int) get_input('offset');
    $limit = 10;

    $query = $org->query_partnerships()->limit($limit, $offset);
    
    $count = $query->count();
    $entities = $query->filter();
    
    echo view_entity_list($entities, $count, $offset, $limit);
        
    if (!$count)
    {
        echo __("partner:none");
    }
?>
</div>