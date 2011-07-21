<?php

    $key = $vars['key'];
    $base_lang = $vars['base_lang'];
    
    $url = isset($vars['base_url']) ? ($vars['base_url'] . "/" . urlencode_alpha($key->name)) : $key->get_url();
    
    echo "<tr>";
    echo "<td style='font-weight:bold'><a href='".escape($url)."'>".escape($key->name)."</a></td>";
    echo "<td>".$key->view_value($key->get_value_in_lang($base_lang))."</td>";
    echo "<td>";
    if ($key->best_translation)
    {
        echo $key->view_value($key->best_translation);
    }
    else
    {
        echo "&nbsp;";
    }
    echo "</td>";        
    echo "</tr>";