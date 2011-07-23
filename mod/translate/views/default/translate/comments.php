<?php
    $query = $vars['query'];
    $language = $vars['language'];

    $offset = (int)get_input('offset');
    $limit = 10;
    $comments = $query->limit($limit, $offset)->filter();
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
    echo "<th>".__('itrans:comment')."</th>";    
    echo "<th style='white-space:nowrap'>".__('itrans:time_created')."</th>";    
    echo "</tr>";
    
    foreach ($comments as $comment)
    {
        $key = $comment->get_key_in_language($language);
        if (!$key)
        {    
            continue;
        }        
        echo "<tr>";
        echo "<td>";
        echo "<strong><a href='{$key->get_url()}'>".escape($key->name)."</a></strong>";
        echo "</td>";
        echo "<td>".$key->view_value($key->get_current_base_value(), 500)."</td>";
        echo "<td>".$key->view_value($key->best_translation, 500)."</td>";
        echo "<td>";
        echo $comment->render_content();
        echo "</td>";
        echo "<td>";
        echo strtr(__('date:date_name'), array(
            '{date}' => $comment->get_date_text(),
            '{name}' => escape($comment->get_owner_name())
        ));    
        echo "</td>";
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