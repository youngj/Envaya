<?php
    $language = $vars['language'];
    $keys = $vars['keys'];
    $group = $vars['group'];             
        
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
        echo view('translate/interface_key_row', array(
            'key' => $keys[$i], 
            'base_url' => $vars['base_url'],
            'base_lang' => $base_lang
        ));        
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