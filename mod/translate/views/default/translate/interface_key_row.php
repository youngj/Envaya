<?php

    $key = $vars['key'];
    $base_lang = $vars['base_lang'];
        
    $output_view = $key->get_output_view();
    
    echo "<tr>";
    echo "<td style='font-weight:bold'><a href='{$key->get_url()}'>".escape($key->name)."</a></td>";
    echo "<td>".view($output_view, array('value' => __($key->name, $base_lang)))."</td>";
    echo "<td>";
    if ($key->best_translation)
    {
        echo view($output_view, array('value' => $key->best_translation));
    }
    else
    {
        echo "&nbsp;";
    }
    echo "</td>";        
    echo "</tr>";