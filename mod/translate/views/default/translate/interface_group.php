<?php
    $group = $vars['group'];    
    
    $query = get_input('q');
    
    $language = $group->get_container_entity();
    $keys = $group->get_available_keys();
    
    $base_lang = Language::get_current_code();
    if ($base_lang == $language->code) // no sense translating from one language to itself
    {
        $base_lang = Config::get('language');
    }

    if ($query)
    {
        $filtered_keys = array();
        foreach ($keys as $key)
        {
            $lq = strtolower($query);
            if (strpos($key->name, $lq) !== false
                || strpos(strtolower(__($key->name)), $lq) !== false
                || strpos(strtolower($key->best_translation), $lq) !== false)
            {
                $filtered_keys[] = $key;
            }
        }
        $keys = $filtered_keys;
    }           
    
    $offset = (int)get_input('offset');
    $limit = 15;
    $count = sizeof($keys);    

    echo "<form method='GET' action='{$group->get_url()}' >";
    echo "<label>".__("itrans:filter")."</label> ";
    echo view('input/text', array(
        'name' => 'q', 
        'js' => "style='width:200px;margin:0px'",
        'value' => $query));
    echo view('input/submit', array('value' => __("search"), 'js' => "style='margin:0px;padding:0px'"));
    echo "</form>";

    echo view('pagination',array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));
?>
<table class='gridTable'>
<?php
    echo "<tr>";
    echo "<th>".__('itrans:language_key')."</th>";
    echo "<th>".__("lang:$base_lang")."</th>";
    echo "<th>".escape($language->name)."</th>";
    echo "</tr>";
        
    for ($i = $offset; $i < $offset + $limit && $i >= 0 && $i < $count; $i++)
    {
        $key = $keys[$i];
        
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
    }    
?>
</table>
<?php
    echo view('pagination',array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));
?>