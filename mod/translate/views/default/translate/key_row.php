<?php

    $key = $vars['key'];
    
    $url = isset($vars['base_url']) ? ($vars['base_url'] . "/" . urlencode_alpha($key->name)) : $key->get_url();
    
    echo "<tr>";
    
    if ($key->can_view())
    {
        echo "<td style='font-weight:bold'>";
        
        echo "<a href='".escape($url)."'>".escape($key->name)."</a>";
        
        echo "</td>";
        
        echo "<td>";
        
        echo $key->view_value($key->get_current_base_value(), 500);
        
        echo "</td>";
        echo "<td>";
        
            if ($key->best_translation)
            {
                echo $key->view_value($key->best_translation, 500);
            }
            else
            {
                echo "&nbsp;";
            }
        echo "</td>";        
    }
    else
    {
        echo "<td colspan='3' style='color:#999'>".__('itrans:hidden')."</td>";
    }
    echo "</tr>";