<?php

$report = $vars['report'];
$org = $report->get_container_entity();
$edit = @$vars['edit'];

$html = $edit ? 'edit_html' : 'view_html';
$input = $edit ? 'edit_input' : 'view_input';

?>  

<?php echo $report->get_field('full_name')->$html(); ?>
<?php echo $report->get_field('other_name')->$html(); ?>
<?php echo $report->get_field('project_name')->$html(); ?>
<?php echo $report->get_field('reference_num')->$html(); ?>

<div class='input'>

<?php 
    $report_period = $report->get_field('report_period');
    echo "<label>".$report_period->label()."</label><br />";
    if ($edit) {
        echo "<div class='help'>".$report_period->help()."</div>";
    }

    $report_dates = $report->get_field('report_dates');
    $report_quarters = $report->get_field('report_quarters');
    
    echo "<table><tr><td style='padding-right:10px'>";
    echo $report_dates->label().": ".$report_dates->$input();
    echo "</td><td>";
    echo $report_quarters->label().": ".$report_quarters->$input();
    echo "</td></tr></table>";
?>
</div>

<?php echo $report->get_field('project_coordinator')->$html(); ?>