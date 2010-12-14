<div class='section_content padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $reports = $org->query_reports()
        ->where('status >= ?', ReportStatus::Approved)
        ->filter();
    
    if (!sizeof($reports))
    {
        echo escape(__("report:none_published"));
    }
    else
    {
        foreach ($reports as $report)
        {
            echo "<div>";
            echo "<a href='{$report->get_url()}'>".escape($report->get_title())."</a>";
            echo " <span class='blog_date'>".$report->get_date_text()."</span>";
            echo "</div>";
        }
    }
?>
</div>