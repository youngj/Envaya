<?php
    $query = $vars['query'];
    $language = $vars['language'];

    $base_lang = Language::get_current_code();
    if ($base_lang == $language->code) // no sense translating from one language to itself
    {
        $base_lang = Config::get('language');
    }    
    
    $offset = (int)get_input('offset');
    $limit = 15;
    $new_translations = $query->limit($limit, $offset)->filter();
    $count = $query->count();    

    echo view('pagination', array(
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
    echo "<th>".__('itrans:translator')."</th>";    
    echo "<th style='white-space:nowrap'>".__('itrans:time_created')."</th>";    
    echo "</tr>";
    
    foreach ($new_translations as $translation)
    {
        echo "<tr>";
        $key = $translation->get_container_entity();
        echo "<td>";
        echo "<strong><a href='{$key->get_url()}'>".escape($key->name)."</a></strong>";
        echo "</td>";
        $view_name = $key->get_output_view();
        echo "<td>".view($view_name, array('value' => __($key->name, $base_lang)))."</td>";
        echo "<td>".view($view_name, array('value' => $translation->value))."</td>";
        echo "<td>";
        echo $translation->get_owner_link();
        echo "</td>";
        echo "<td>".friendly_time($translation->time_created)."</td>";
        echo "</tr>";
    }
?>
</table>
<?php
    echo view('pagination', array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));
?>
</div>