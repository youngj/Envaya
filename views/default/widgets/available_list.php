<?php
    $container = $vars['container'];
    $mode = $vars['mode'];
    
    echo view('widgets/edit_list', array('container' => $container));

    echo "<div style='border-top:1px solid #ccc;margin-top:7px;margin-bottom:5px'></div>";        
    
    $widgets = $container->get_available_widgets($mode);    
    
    $from = escape(urlencode($_SERVER['REQUEST_URI']));
    
    $widget_list = array();    
    foreach ($widgets as $widget)
    {
        $widget_list[] = "<a style='color:#666' href='{$widget->get_edit_url()}?from=$from'>".
                escape($widget->get_title())."</a>";
    }
    
    if (@$vars['add_link_url'])
    {
        $widget_list[] = "<a style='color:#666' href='{$vars['add_link_url']}?from=$from'>".
                $vars['add_link_text']."</a>";
    }
    
    if (sizeof($widget_list))
    {               
        echo "<div class='widget_list'>";
        echo implode('<br />', $widget_list);
        echo "</div>";
    }
