<div class='section_content padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $report_defs = $org->query_report_definitions()->filter();
    
    if (!sizeof($report_defs))
    {
        echo escape(__("report:none_defined"));
    }
    else
    {
        foreach ($report_defs as $report_def)
        {
            echo "<div>";
            echo "<a href='{$report_def->get_url()}'>".escape($report_def->get_title())."</a>";            
            $num_reports = $report_def->query_reports()->where('status = ?', ReportStatus::Approved)->count();            
            echo " ($num_reports)";            
            echo "</div>";
        }
    }
?>
</div>