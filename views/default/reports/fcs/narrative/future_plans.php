<?php

$report = $vars['report'];
$org = $report->get_container_entity();
$edit = @$vars['edit'];

$html = $edit ? 'edit_html' : 'view_html';
$input = $edit ? 'edit_input' : 'view_input';

?>  

<?php echo $report->get_field('future_activities')->$html(); ?>
