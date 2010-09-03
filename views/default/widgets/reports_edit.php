<div class='padded section_content'>
<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    $reports = $org->query_reports()->filter();
    
    if ($reports)
    {
?>
<table class='gridTable'>
<tr class='header_row'>
    <th>Report</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php    

    $escUrl = $_SERVER['REQUEST_URI'];
    
    foreach ($reports as $report)
    {
        $count += 1;
        $rowClass = (($count % 2) != 0) ? 'odd' : 'even';
       
        echo "<tr class='$rowClass'>";
        echo "<td>".escape($report->get_title())."</td>";
        echo "<td style='white-space:nowrap'>";        
        echo escape($report->get_status_text());        
        echo "</td>";        
        echo "<td style='white-space:nowrap'>";
        
        $status = $report->status;
        if ($status == ReportStatus::Blank)
        {
            echo "<a href='{$report->get_url()}/edit?from=$escUrl'>".__("report:start")."</a>";
        }
        else if ($status == ReportStatus::Draft)
        {
            echo "<a href='{$report->get_url()}/preview'>".__("report:view_draft")."</a><br />";   
            echo "<a href='{$report->get_url()}/edit'>".__("report:continue_editing")."</a><br />";              
            echo "<a href='{$report->get_url()}/confirm_submit'>".__("report:submit")."</a>";              
        }
        else
        {
            echo "<a href='{$report->get_url()}'>".__("report:view_report")."</a> ";   
        }
        
        echo "</td>";
        echo "</tr>";
    }    
?>
</table>

<?php
    }
    else
    {
        echo __('report:none_available');
    }
?>
</div>