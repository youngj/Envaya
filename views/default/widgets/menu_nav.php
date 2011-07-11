<?php

    $content = $vars['content'];
    $widget = $vars['widget'];
    
    $container = $widget->get_container_entity();
    
    echo "<div class='section_content'>";
    
    echo "<div class='padded' style='padding-bottom:0px;padding-top:0px;color:#666'>";
    echo view('breadcrumb', array(
        'separator' => ' : ', 
        'include_last' => false,
        'items' => $widget->get_breadcrumb_items())
    );
    echo "<h2>".escape($widget->get_title())."</h2>";
    echo "</div>";
    
    echo $content;
    
    echo "<div class='padded'>";

    $prev_sibling = $container->query_published_widgets()
        ->where('menu_order < ?', $widget->menu_order)
        ->order_by('menu_order desc')
        ->get();    
        
    $next_sibling = $container->query_published_widgets()
        ->where('menu_order > ?', $widget->menu_order)
        ->order_by('menu_order')
        ->get();   
    
    if ($next_sibling)
    {
        echo "<div>";
        echo __('next').': ';
        echo "<a href='{$next_sibling->get_url()}'>".escape($next_sibling->get_title())."</a>";
        echo "</div>";
    }        
        
    if ($prev_sibling)
    {
        echo "<div>";
        echo __('previous').': ';
        echo "<a href='{$prev_sibling->get_url()}'>".escape($prev_sibling->get_title())."</a>";
        echo "</div>";
    }
    
    echo "</div>";
    
    echo "</div>";