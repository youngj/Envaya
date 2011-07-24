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
    $filter = $vars['filter'];
    
    $query = @$filter['q'];
    $status = @$filter['status'];
    
    $filter_parts = array();
    foreach ($filter as $k => $v)
    {
        $filter_parts[] = urlencode_alpha($k).'='.urlencode_alpha($v);
    }
    $filter_str = implode(',', $filter_parts);    
        
    $language = $group->get_container_entity();    
    
    $count = sizeof($filtered_keys);    
                                
    echo "<form method='GET' id='filter_form' action='{$group->get_url()}'>";
    echo "<label>".__("itrans:filter")."</label> ";
    echo view('input/text', array(
        'name' => 'q', 
        'style' => "width:150px;margin:0px",
        'attrs' => array(
            'onchange' => '$("filter_form").submit()'
        ),        
        'value' => $query
    ));
    echo view('input/pulldown', array(
        'name' => 'status',
        'options' => array(
            '' => __('itrans:status_all'),
            'empty' => __('itrans:not_translated'),
            'notempty' => __('itrans:translated'),
        ),
        'attrs' => array(
            'onchange' => '$("filter_form").submit()'
        ),
        'value' => $status,
    ));
        
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
    
    echo view('translate/key_table', array(
        'keys' => $filtered_keys,
        'language' => $language,
        'base_url' => "/tr/{$language->code}/module/{$group->name}" . ($filter_str ? ",$filter_str" : '')
    ));
