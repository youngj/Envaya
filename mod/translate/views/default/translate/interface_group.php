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
    $base_url = $vars['base_url'];
                 
    $language = $group->get_container_entity();    
    
    $count = sizeof($filtered_keys);    
    
    echo view('translate/filter_form', array(
        'action' => $group->get_url(),
        'filter' => $filter,
    ));
        
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
        'base_url' => $base_url
    ));
