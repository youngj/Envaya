<div class='section_content padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $reports = $org->queryReports()->where('status = ?', ReportStatus::Published)->filter();
    
    foreach ($reports as $report)
    {
        echo "<div>";
        echo "<a href='{$report->getURL()}'>".escape($report->getTitle())."</a>";
        echo "<span class='blog_date'>".$report->getDateText()."</span>";
        echo "</div>";
    }
?>
</div>