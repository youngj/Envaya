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
    
    $base_lang = $language->get_current_base_code();
    
    $offset = (int)get_input('offset');
    $limit = 15;
    $count = sizeof($filtered_keys);    
                                
    echo "<form method='GET' action='{$group->get_url()}'>";
    echo "<label>".__("itrans:filter")."</label> ";
    echo view('input/text', array(
        'name' => 'q', 
        'style' => "width:150px;margin:0px",
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
        
    echo view('input/submit', array('value' => __("search"), 'style' => "margin:0px;padding:0px"));
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
        echo view('translate/interface_key_row', array('key' => $filtered_keys[$i], 'base_lang' => $base_lang));
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