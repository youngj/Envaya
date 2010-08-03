<div class='section_content padded'>
<?php 
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $offset = (int) get_input('offset');
    $limit = 10;

    $query = $org->queryPartnerships()->limit($limit, $offset);
    
    $count = $query->count();
    $entities = $query->filter();
    
    echo view_entity_list($entities, $count, $offset, $limit);
        
    if (!$count)
    {
        echo __("partner:none");
    }
?>
</div>