<?php
    $language = $vars['language'];
    $key_names = $vars['key_names'];
    $group = $vars['group'];         
    
    $keys = $language->query_keys()
        ->where_in('name', $key_names)
        ->order_by('name')
        ->filter();
        
    $keys_map = array();
    foreach ($keys as $key)
    {
        $keys_map[$key->name] = $key;
    }
    
    $base_lang = $language->get_current_base_code();
    
    $offset = (int)get_input('offset');
    $limit = 15;
    $count = sizeof($keys);    
                                
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
        echo view('translate/interface_key_row', array('key' => $keys[$i], 'base_lang' => $base_lang));        
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