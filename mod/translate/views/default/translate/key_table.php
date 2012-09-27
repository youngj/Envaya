<?php    
    $keys = @$vars['keys'];
    $query = @$vars['query'];
    
    $base_url = $vars['base_url'];
    $language = $vars['language'];
    
    $limit = 15;
    $offset = Input::get_int('offset');    
    
    if (isset($keys))
    {
        $count = sizeof($keys);        
        $visible_keys = array_slice($keys, $offset, $limit);
    }
    else
    {
        $visible_keys = $query->limit($limit, $offset)->filter();        
        $count = null; //$query->count();
    }       
    
    echo view('pagination',array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit,
        'count_displayed' => sizeof($visible_keys),
    ));   
?>
<table class='keyTable'>
<thead>
<?php
    echo "<tr>";
    //echo "<th>".__('itrans:language_key')."</th>";
    echo "<th>".__("itrans:base_lang")."</th>";
    echo "<th>".escape($language->name)."</th>";
    echo "</tr>";    
?>
</thead>
<tbody>
<?php
        
    foreach ($visible_keys as $key)
    {
        echo view('translate/key_row', array(
            'key' => $key, 
            'base_url' => $base_url,
        ));    
    }
?>
</tbody>
</table>
<?php
    echo view('pagination',array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit,
        'count_displayed' => sizeof($visible_keys),
    ));
?>