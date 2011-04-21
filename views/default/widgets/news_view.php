<div class='section_content'>
<?php 
    $widget = $vars['widget'];
    
    $end_guid = $vars['end_guid'];
    $offset = (int) get_input('offset');
    
    if ($end_guid)
    {
        $end_widget = Widget::get_by_guid($end_guid);    
        if ($end_widget)
        {
            $offset = $widget->query_widgets()
                ->where('time_created > ?', $end_widget->time_created)
                ->where('status = ?', EntityStatus::Enabled)
                ->count();
        }
    }

    $limit = 10;    
    $query = $widget->query_widgets()
        ->order_by('time_created desc')
        ->where('status = ?', EntityStatus::Enabled);
        
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
        'baseurl' => $widget->get_url(),
    ));        
    
    if (!$count)
    {
        echo "<div class='padded'>".__("widget:news:empty")."</div>";
    }

?>
</div>