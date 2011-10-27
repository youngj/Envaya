<?php
    $widget = $vars['widget'];
    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $widget->query_widgets()->count();
    $updates = $widget->query_widgets()
        ->order_by('publish_status asc, time_published desc, guid desc') // show draft posts first
        ->limit($limit, $offset)
        ->filter();

    echo view("section", array(
        'header' => __("widget:news:manage_updates"),
    ));            
    
    if ($count)
    {
        echo view('paged_list',array(
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
            'items' => array_map(function($update) { 
                return view('widgets/news_edit_post_item', array('widget' => $update));
            }, $updates),
            'separator' => "<div class='separator'></div>"
        ));        
    }
    else
    {
        echo "<div class='section_content padded'>".__("widget:news:empty")."</div>";
    }
?>
