<?php
    $container = $vars['container'];
    
    $widgets = $container->query_widgets()->filter();    
    $from = escape(urlencode($_SERVER['REQUEST_URI']));
    
    $menu_widgets = array();
    $non_menu_widgets = array();
    
    $include_count = $vars['include_count'];
    $can_move_up = false;
    $table_id = "widget_list_{$include_count}";
    
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
                json_encode("{$widget->get_base_url()}/reorder?delta=-1").",\"{$table_id}\",\"{$link_id}\");";
            $style = $can_move_up ? '' : 'display:none';
            echo "<a href='$url' style='$style' id='$link_id' onclick='ignoreDirty()' style='text-decoration:none'>&uarr;</a>";
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
        if ($include_count == 0)
        {
?>

<script type='text/javascript'>
function asyncReorderWidget($url, $table_id, $link_id)
{
    var xhr = getXHR(function(res) {             
        var guids = res.guids;        
        var rows = {};        
        var tbody = document.getElementById($table_id);

        for (var i = 0; i < guids.length; i++)
        {
            var guid = guids[i];
            var row = document.getElementById($table_id + '_' + guid);
            if (row)
            {
                removeElem(row);
                rows[guid] = row;
            }
        }
        
        for (var i = 0; i < guids.length; i++)
        {
            var guid = guids[i];
            var row = rows[guid];
            if (row)
            {
                tbody.appendChild(row);                
                var up = document.getElementById($table_id + '_' + guid + '_up');
                if (up) 
                {
                    up.style.display = (i == 0) ? 'none' : 'inline';
                }
            }
        }
    });
        
    var link = document.getElementById($link_id);        
    link.style.display = 'none';
    
    xhr.open("POST", $url, true);       
    
    var form = document.forms[0];
    var params = "<?php 
        $ts = time();
        $token = generate_security_token($ts);        
        echo "__ts=$ts&__token=$token";
    ?>";
    
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Content-Length", params.length);            
    xhr.send(params);
}
</script>
<?php
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
