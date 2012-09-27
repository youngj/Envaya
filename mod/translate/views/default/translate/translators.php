<?php
    $language = $vars['language'];
    
    $query = $language->query_translator_stats()->order_by('score desc');

    $limit = 20;
    $offset = Input::get_int('offset');
    
    $stats_list = $query->limit($limit, $offset)->filter();
    $count = $query->count();
    
    echo view('pagination', array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));    
?>
<table class='gridTable' style='width:600px'>
<tr>
    <th><?php echo __('user:name:label'); ?></th>
    <th><?php echo __('itrans:last_date'); ?></th>
    <th><?php echo __('itrans:translations'); ?></th>
    <th><?php echo __('itrans:votes'); ?></th>
</tr>    

<?php    
    
    foreach ($stats_list as $stats)
    {
?>
<tr>
    <td><strong><?php echo "<a href='{$stats->get_url()}'>".escape($stats->get_display_name())."</a>"; ?></strong></td>
    <td><?php echo friendly_time($stats->time_updated); ?></td>
    <td><?php echo $stats->num_translations; ?></td>
    <td><?php echo $stats->num_votes; ?></td>
</tr>
<?php
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