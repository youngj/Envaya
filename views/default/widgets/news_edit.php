<?php

    $widget = $vars['widget'];

    echo view('section', array(
        'header' => __("widget:news:add_update"), 
    ));        
    echo view('news/add_post', array('widget' => $widget));    

    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $widget->query_widgets()->count();
    $updates = $widget->query_widgets()
        ->order_by('guid desc')
        ->limit($limit, $offset)
        ->filter();

    ob_start();

    if ($count)
    {
        $elements = array();
        
        foreach ($updates as $update)
        {        
            ob_start();
            
            echo "<div style='padding:4px 0px'>";
            echo "<div style='float:right;margin-left:5px'>";
            echo "<a href='{$update->get_edit_url()}?from=$escUrl'>".__("edit")."</a>";
            echo " &middot; ";
            echo view('input/post_link', array(
                'href' => "{$update->get_edit_url()}?delete=1",
                'confirm' => __('widget:news:delete_confirm'),
                'text' => __('delete')
            ));
            
            echo "</div>";
            if ($update->title)
            {
                echo "<strong>".escape($update->title)."</strong><br />";
            }
            if ($update->thumbnail_url)
            {
                echo "<img src='".escape($update->thumbnail_url)."' style='display:block;padding:2px' />";
            }
            echo $update->get_snippet();
            echo "<div class='blog_date'>";
            echo "<a href='{$update->get_edit_url()}?from=$escUrl'>{$update->get_date_text()}</a>";
            if ($update->publish_status == Widget::Draft)
            {
                echo " (".__('widget:draft').")";
            }
            echo "</div>";            
            echo "</div>";
            
            $elements[] = ob_get_clean();
        }
        
        echo view('paged_list',array(
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
            'elements' => $elements,
            'separator' => "<div class='separator'></div>"
        ));        
    }
    else
    {
        echo "<div>".__("widget:news:empty")."</div>";
    }

    $content = ob_get_clean();
    
    $form = view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content,
        'noSave' => true,
    ));    

    echo view("section", array(
        'header' => __("widget:news:manage_updates"),
        'content' => $form
    ));
?>
