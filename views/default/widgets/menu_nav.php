<?php

    $content = $vars['content'];
    $widget = $vars['widget'];
    
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
    
    $get_sibling = function($widget, $cmp, $sort)
    {        
        $cur = $widget;
        while (true)            
        {
            $container = $cur->get_container_entity();
            if ($container == null || !($container instanceof Widget_Menu))
            {
                break;
            }           
            
            $sibling = $container->query_published_widgets()
                ->where("menu_order $cmp ?", $cur->menu_order)
                ->order_by("menu_order $sort")
                ->get();
                
            if ($sibling)
            {
                return $sibling;
            }   
            
            else if ($cmp == '<')
            {
                return $container;
            }
            
            $cur = $container;            
        }
        return null;
    };  
    
    $prev_sibling = $get_sibling($widget, '<', 'desc');
    $next_sibling = $get_sibling($widget, '>', 'asc');
        
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