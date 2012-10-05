<?php
    $container = $vars['container'];
    
    $widgets = $container->query_widgets()->where('publish_status <> ?', Widget::Deleted)->filter();    
    $from = escape(urlencode($_SERVER['REQUEST_URI']));
    
    $menu_widgets = array();
    $non_menu_widgets = array();
    
    $can_move_up = false;
    $table_id = "widget_list_{$INCLUDE_COUNT}";
    
    $ts = timestamp();
    $token = Input::generate_security_token($ts);
    
    foreach ($widgets as $widget)
    {
        $link = "<a style='font-weight:bold' href='{$widget->get_edit_url()}?from=$from'>".
                        escape($widget->get_title())."</a>";
    
        if ($widget->publish_status == Widget::Draft)
        {
            $link .= " (".__('widget:draft').")";
        }
            
        if ($widget->in_menu)
        {
            ob_start();
            
            echo "<tr id='{$table_id}_{$widget->guid}'><td>";
            echo $link;            
            echo "</td><td style='text-align:right;color:#999'>";
            $link_id = "{$table_id}_{$widget->guid}_up";
            $url = "javascript:asyncReorderWidget(".
                json_encode("{$widget->get_base_url()}/reorder?delta=-1").",\"{$table_id}\",\"{$link_id}\", \"$ts\", \"$token\");";
            $style = $can_move_up ? '' : 'display:none';
            echo "<a href='$url' style='$style' id='$link_id' onmouseover='highlightRow(\"{$table_id}_{$widget->guid}\", true)' onmouseout='highlightRow(\"{$table_id}_{$widget->guid}\", false)' onclick='ignoreDirty()' style='text-decoration:none'>&uarr;</a>";
            echo "</td>";
            echo "</td></tr>";
                        
            $menu_widgets[] = ob_get_clean();
            
            $can_move_up = true;
        }
        else
        {
            $non_menu_widgets[] = $link;
        }
    }
     
    if (sizeof($menu_widgets))
    {
        if ($INCLUDE_COUNT == 0)
        {
            echo view('js/reorder_widget');
        }
    
        echo "<table style='width:100%' class='widget_list'><tbody id='$table_id'>";
        echo implode('', $menu_widgets);
        echo "</tbody></table>";
    }
    
    if (sizeof($non_menu_widgets))
    {
        echo "<div class='widget_list'>";
        echo implode('<br />', $non_menu_widgets);
        echo "</div>";    
    }
