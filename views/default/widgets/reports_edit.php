<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
?>
<table>
<tr class='header_row'>
    <th colspan='3'>Report</th>
    <th><div class='header_icons edit_icon'></div></th>
</tr>

<?php
    $reports = $org->queryReports()->filter();

    $escUrl = $_SERVER['REQUEST_URI'];
    
    foreach ($reports as $report)
    {
        $count += 1;
        $rowClass = (($count % 2) != 0) ? 'odd' : 'even';
       
        echo "<tr class='$rowClass'>";
        echo "<td>".escape($report->getTitle())."</td>";
        echo "<td><span class='blog_date'>{$report->getDateText()}</span></td>";
        echo "<td><a href='{$report->getURL()}'>".__("view")."</a></td>";
        echo "<td><a href='{$report->getURL()}/edit?from=$escUrl'>".__("edit")."</a></td>";
        echo "</tr>";
    }    
?>
</table>
