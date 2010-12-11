<div class='section_content padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $report_defs = $org->query_report_definitions()->filter();
    
    if (!sizeof($report_defs))
    {
        echo escape(__("report:none_available"));
    }
    else
    {      
        echo "<table class='gridTable'>";
        echo "<tr><th>Report</th><th>Actions</th></tr>";
    
        $count  = 0;
        foreach ($report_defs as $report_def)
        {
            $count += 1;
            $row_class = $count % 2 ? 'odd' : 'even';
            $num_reports = $report_def->query_reports()->count();
        
            echo "<tr class='$row_class'>";
            echo "<td>";
            echo "<a href='{$report_def->get_url()}'>".escape($report_def->get_title())."</a>";            
            echo "</td>";
            
            echo "<td>";            
            echo "<div><a href='{$report_def->get_url()}/edit'>Preview Report</a></div>";
            echo "<div><a href='{$report_def->get_url()}/edit?tab=invite'>Invite Organizations</a></div>";
            echo "<div><a href='{$report_def->get_url()}/edit?tab=manage'>Manage Responses</a></div>";
            echo "<div><a href='{$report_def->get_url()}/edit?tab=export'>Export Responses</a></div>";
            
            echo "<div>";
            
            if ($num_reports == 0)
            {            
                echo view('output/confirmlink', array(
                    'is_action' => true,
                    'text' => 'Delete Report',
                    'href' => "{$report_def->get_url()}/delete"
                ));
            }
            
            echo "</div>";
            
            echo "</td>";
            
            echo "</tr>";
        }        
        echo "</table>";
    }
    ?>
    
    <br><br>
    <a href='<?php echo $org->get_url(); ?>/reporting/add'>Add New Report</a>
</div>