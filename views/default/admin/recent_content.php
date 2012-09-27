<div class='section_content padded'>
<?php

    $content_filter = $vars['content_filter'];

    $query = Widget::query();

    if ($content_filter)
    {
        $query->where("content like ?", $content_filter);
    }
    $query->where('publish_status = ?', Widget::Published);
    $query->order_by('time_updated desc');
    
    $limit = 10;
    $offset = Input::get_int('offset');

    $query->limit($limit, $offset);
        
    $widgets = $query->filter();

    $items = array();
    foreach ($widgets as $widget)
    {
        ob_start();
        
        $org = $widget->get_container_user();
        echo "<h3 style='padding:5px 0px'><a href='{$widget->get_url()}'>".escape($org->name).": ".escape($widget->get_title())."</a></h3>";
        
        echo $widget->render_content();
        echo "<div class='blog_date'>";
        echo "<a href='{$widget->get_url()}'>";
        echo $widget->get_date_text();
        echo "</a>";
        echo "</div>";
        
        $items[] = ob_get_clean();
    }

    echo view('paged_list', array(
        'offset' => $offset,
        'limit' => $limit,
        'count' => null,
        'count_displayed' => sizeof($items),
        'items' => $items,
        'separator' => "<div class='separator'></div>"
    ));    
    
?>
</div>