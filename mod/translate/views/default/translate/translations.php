<?php
    $query = $vars['query'];
    $language = $vars['language'];
    
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
    echo "<th>".__("itrans:base_lang")."</th>";
    echo "<th>".escape($language->name)."</th>";    
    echo "<th>".__('itrans:translator')."</th>";    
    echo "<th style='white-space:nowrap'>".__('itrans:time_created')."</th>";    
    echo "</tr>";
    
    foreach ($new_translations as $translation)
    {
        echo "<tr>";
        $key = $translation->get_container_entity();
        
        if ($key && Permission_ViewTranslation::has_for_entity($key))
        {
            echo "<td>";
            echo "<strong><a href='{$key->get_url()}'>".escape($key->name)."</a></strong>";
            echo "</td>";
            echo "<td>".$key->view_value($key->get_current_base_value(), 500)."</td>";
            echo "<td>".$key->view_value($translation->value, 500)."</td>";
            echo "<td>";
            echo $translation->get_owner_link();
            echo "</td>";
            echo "<td>".friendly_time($translation->time_created)."</td>";
        }
        else
        {
            echo "<td colspan='5' style='color:#999'>".__('itrans:hidden')."</td>";
        }
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