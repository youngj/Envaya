<?php

    $key = $vars['key'];
    
    $url = isset($vars['base_url']) ? ($vars['base_url'] . "/" . urlencode_alpha($key->name)) : $key->get_url();
    $escUrl = escape($url);
    
    echo "<tr>";
    
    if (Permission_ViewTranslation::has_for_entity($key))
    {
        //echo "<td style='font-weight:bold'>";        
        //echo "<a href='$escUrl'>".escape($key->name)."</a>";        
        //echo "</td>";
        
        echo "<td style='width:350px;'>";
        echo "<a style='color:#333' href='$escUrl'>";
        echo $key->view_value($key->get_current_base_value(), 500);
        echo "</a>";        
        echo "</td>";
        echo "<td style='width:350px;'>";        
        
        echo "<a style='color:#333' href='$escUrl'>";
    
        if ($key->best_translation)
        {
            echo $key->view_value($key->best_translation, 500);
        }
        else
        {
            echo "<span style='color:#ccc'>(".__('itrans:not_translated').")</span>";
        }
        echo "</a>";
        echo "</td>"; 
        echo "<td>";
        echo "<a href='$escUrl'>".__('edit')."</a>";
        echo "</td>";
    }
    else
    {
        echo "<td colspan='3' style='color:#ccc'>".__('itrans:hidden')."</td>";
    }
    echo "</tr>";