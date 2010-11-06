<?php
$report = $vars['report'];
$html = @$vars['edit'] ? 'edit_html' : 'view_html';
?>  

<?php echo $report->get_field('events_attended')->$html(); ?>
