<?php

    $content = $vars['content'];
    $widget = $vars['widget'];
        
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
    
    echo "<div class='section_content'>";
    
    echo view('widgets/breadcrumb_heading', array('widget' => $widget));   
    
    echo $content;
    
    echo "<div class='padded'>";
        
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