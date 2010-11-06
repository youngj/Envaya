<?php
$report = $vars['report'];
$html = @$vars['edit'] ? 'edit_html' : 'view_html';

?>  

<?php echo $report->get_field('outputs')->$html(); ?>
<?php echo $report->get_field('planned_activities')->$html(); ?>
<?php echo $report->get_field('achievements')->$html(); ?>
<?php echo $report->get_field('difference_reason')->$html(); ?>
<?php echo $report->get_field('resources_used')->$html(); ?>
