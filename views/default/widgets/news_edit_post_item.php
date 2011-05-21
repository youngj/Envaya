<?php
    $widget = $vars['widget'];

    $escUrl = urlencode($_SERVER['REQUEST_URI']);
    
    echo "<div class='padded'>";
    echo "<div style='float:right;margin-left:5px'>";
    echo "<a href='{$widget->get_edit_url()}?from=$escUrl'>".__("edit")."</a>";
    echo " &middot; ";
    echo view('input/post_link', array(
        'href' => "{$widget->get_edit_url()}?delete=1",
        'confirm' => __('widget:news:delete_confirm'),
        'text' => __('delete')
    ));
    
    echo "</div>";
    echo view($widget->get_title_view(), array('widget' => $widget, 'is_primary' => true));
    
    if ($widget->thumbnail_url)
    {
        echo "<img src='".escape($widget->thumbnail_url)."' style='display:block;padding:2px' />";
    }
    echo $widget->get_snippet(300);                
    echo view($widget->get_date_view(), array('widget' => $widget, 'is_primary' => false));
    echo "</div>";