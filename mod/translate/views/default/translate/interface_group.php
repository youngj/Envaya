<div style='float:right;'>
<ul>
<?php
echo "<li><a href='/tr/instructions#group' target='_blank'>".__('itrans:instructions')."</a></li>";     
?>
</ul>
</div>
<?php
    $group = $vars['group'];    
    $filtered_keys = $vars['filtered_keys'];
    $all_keys = $vars['all_keys'];
    
    $query = Session::get('translate_filter_query');
    $status = Session::get('translate_filter_status');
        
    $language = $group->get_container_entity();    
    
    $base_lang = Language::get_current_code();
    if ($base_lang == $language->code) // no sense translating from one language to itself
    {
        $base_lang = Config::get('language');
    }
    $time = time();
    
    $offset = (int)get_input('offset');
    $limit = 15;
    $count = sizeof($filtered_keys);    
                                
    echo "<form method='GET' action='{$group->get_url()}'>";
    echo "<label>".__("itrans:filter")."</label> ";
    echo view('input/text', array(
        'name' => 'q', 
        'js' => "style='width:150px;margin:0px'",
        'value' => $query
    ));
    echo view('input/pulldown', array(
        'name' => 'status',
        'options' => array(
            'all' => __('itrans:status_all'),
            'empty' => __('itrans:status_empty'),
            'notempty' => __('itrans:status_notempty'),
        ),
        'value' => $status,
    ));
        
    echo view('input/submit', array('value' => __("search"), 'js' => "style='margin:0px;padding:0px'"));
    echo "</form>";
    
    echo "<div style='padding-bottom:5px;'>";
    echo "<label>".__('itrans:progress').":</label> ";
    
    $not_empty = function($key) { return $key->best_translation != ''; };   
    $num_not_empty = sizeof(array_filter($all_keys, $not_empty));
    $total = sizeof($all_keys);
    
    echo "{$num_not_empty} / {$total}";
    
    if ($count != $total)
    {
        $num_not_empty_in_filter = sizeof(array_filter($filtered_keys, $not_empty));

        echo " (".sprintf(__('itrans:in_filter'), "{$num_not_empty_in_filter} / {$count}").")";
    }
    
    echo "</div>";

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
        $key = $filtered_keys[$i];
        
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