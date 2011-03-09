<?php
    $report = $vars['report'];
    $input = @$vars['edit'] ? 'edit_input' : 'view_input';

    $report_dates = $report->get_field('report_dates');
    $report_quarters = $report->get_field('report_quarters');

    echo "<table class='noBorderTable'><tr><td style='padding-right:10px'>";
    echo $report_dates->label().": ".$report_dates->$input();
    echo "</td><td>";
    echo $report_quarters->label().": ".$report_quarters->$input();
    echo "</td></tr></table>";
?>