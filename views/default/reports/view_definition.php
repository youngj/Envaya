<?php
$report_def = $vars['report_def'];
$num_approved = $report_def->query_approved()->count();
?>

<div class='section_content padded'>
<table>
<tr>
<th>Reports Approved:</th>
<td  style='padding-left:10px'><?php echo $num_approved; ?></td>
</tr>
<tr>
<th>Reports Pending Approval:</th>
<td style='padding-left:10px'><?php echo $report_def->query_reports()->where('status = ?', ReportStatus::Submitted)->count(); ?></td>
</tr>
<?php if ($num_approved > 0) { ?>
<tr>
<th>Approved Reports:</th>
<td style='padding-left:10px'>

<?php    

    $reports = $report_def->query_approved()->filter();            
    
    foreach ($reports as $report)
    {
        echo "<div><a href='{$report->get_url()}'>".escape($report->get_container_entity()->name)."</a></div>";
    }      
?>
</td>
</tr>
<?php } ?>
</table>

<br><br>
Export responses as: <a href='<?php echo $report_def->get_url() ?>?view=csv'>CSV</a>
</div>