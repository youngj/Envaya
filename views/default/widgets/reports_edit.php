<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
?>
<table>
<tr class='header_row'>
    <th colspan='3'>Report</th>
    <th><div class='header_icons edit_icon'></div></th>
</tr>

<?php
    $reports = $org->query_reports()->filter();

    $escUrl = $_SERVER['REQUEST_URI'];
    
    foreach ($reports as $report)
    {
        $count += 1;
        $rowClass = (($count % 2) != 0) ? 'odd' : 'even';
       
        echo "<tr class='$rowClass'>";
        echo "<td>".escape($report->get_title())."</td>";
        echo "<td><span class='blog_date'>{$report->get_date_text()}</span></td>";
        echo "<td><a href='{$report->get_url()}'>".__("view")."</a></td>";
        echo "<td><a href='{$report->get_url()}/edit?from=$escUrl'>".__("edit")."</a></td>";
        echo "</tr>";
    }    
?>
</table>
