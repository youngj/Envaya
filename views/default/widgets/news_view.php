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
            $offset = $widget->query_published_widgets()
                ->where('time_published > ?', $end_widget->time_published)
                ->count();
        }
    }

    $limit = 10;    
    $query = $widget->query_published_widgets()
        ->order_by('time_published desc, guid desc');
        
    $count = $query->count();
    $posts = $query->limit($limit, $offset)->filter();            
?>

<?php if (!empty($entities)) { ?>

<div style='clear:both'></div>
<?php } ?>
<?php

    echo view('paged_list', array(
        'items' => array_map(function($p) { return $p->render_view(); }, $posts),
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
        'baseurl' => $widget->get_url(),
        'separator' => "<div class='separator'></div>",
    ));
    
    if (!$count)
    {
        echo "<div class='padded'>".__("widget:news:empty")."</div>";
    }

?>
</div>