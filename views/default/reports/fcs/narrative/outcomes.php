<?php
$report = $vars['report'];
$html = @$vars['edit'] ? 'edit_html' : 'view_html';
?>  

<?php echo $report->get_field('intended_results')->$html(); ?>
<?php echo $report->get_field('actual_outcomes')->$html(); ?>
<?php echo $report->get_field('other_outcomes')->$html(); ?>
<?php echo $report->get_field('outcome_difference_reason')->$html(); ?>

