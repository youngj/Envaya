<?php
    $user = $vars['user'];
    $language = $vars['language'];
    $stats = $vars['stats'];
?>
<table class='gridTable inputTable' style='margin-bottom:10px;width:300px'>
<tr>
    <th><?php echo __('user:name:label'); ?></th>
    <td><?php echo escape($stats->get_display_name()); ?></td>
</tr>
<tr>
    <th><?php echo __('itrans:last_date'); ?></th>
    <td><?php echo friendly_time($stats->time_updated); ?></td>
</tr>
<tr>
    <th><?php echo __('itrans:translations'); ?></th>
    <td><?php echo $stats->num_translations; ?></td>
</tr>
<tr>
    <th><?php echo __('itrans:votes'); ?></th>
    <td><?php echo $stats->num_votes; ?></td>
</tr>
</table>

<?php    
    echo "<h3 style='padding-bottom:5px'>".__('itrans:latest')."</h3>";
    
    $query = $language->query_translations()->where('owner_guid = ?', $user->guid)->order_by('time_created desc, guid desc');

    echo view('translate/translations', array('query' => $query, 'language' => $language));