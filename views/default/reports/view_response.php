<?php
$report = $vars['report'];
?>

<div class='section_content padded report_view'>
<table class='report_info'>
<tr>
    <th>Organization:</td>
    <td>
        <?php
            $org = $report->get_container_entity();
            if ($org)
            {
                echo "<a href='{$org->get_url()}'>".escape($org->name)."</a>";
            }
        ?>
    </td>
</tr>
<tr>
    <th>Date Submitted:</td>
    <td><?php echo escape(get_date_text($report->time_submitted, true)); ?></td>
</tr>
<tr>
    <th>Signature:</td>
    <td>
        <?php 
            echo escape($report->signature);            
        ?>    
    </td>
</tr>
<tr>
    <th>Status:</td>
    <td>
        <?php 
            echo escape($report->get_status_text());            
            
            echo "<br />";
            
            echo view('reports/set_status_links', array('report' => $report));
        ?>    
    </td>
</tr>

</table> 

<?php


echo $report->render_view();

echo view('reports/set_status_links', array('report' => $report));
echo "<br />";
echo "<a href='{$report->get_report_definition()->get_url()}/edit?section=manage'>Return to all Reports</a>";
?>
</div>
