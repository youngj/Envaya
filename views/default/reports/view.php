<?php
$report = $vars['report'];
?>

<div class='section_content padded report_view'>
<table class='report_info'>
<tr>
    <th>Report Sponsor:</td>
    <td>
        <?php
            $report_def = $report->get_report_definition();
            $sponsor = $report_def->get_container_entity();
            if ($sponsor)
            {
                echo "<a href='{$sponsor->get_url()}'>".escape($sponsor->name)."</a>";
            }
        ?>
    </td>
</tr>
<tr>
    <th>Report Name:</td>
    <td>
        <?php 
            $report_def = $report->get_report_definition();
            echo "<a href='{$report_def->get_url()}'>".escape($report_def->get_title())."</a>";            
        ?>    
    </td>
</tr>
<tr>
    <th>Date Submitted:</td>
    <td><?php echo escape(get_date_text($report->time_submitted, true)); ?></td>
</tr>

</table> 

<?php
$report = $vars['report'];
echo $report->render_view();
?>
</div>