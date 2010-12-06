<?php
    $report_def = $vars['report_def'];
    $org = $report_def->get_container_entity();
    
    $tab = $vars['tab'];
    $tabs = array(
        '' => "Preview Report",
        'invite' => "Invite Organizations",
        'manage' => "Manage Responses",
        'export' => "Export Responses",
    );
?>
<div class='section_content padded'>    

<?php
echo "<div class='report_section_nav'>";
$links = array();
foreach ($tabs as $s => $section_title)
{
    if ($tab == $s)
    {
        $links[] = "<span>".escape($section_title)."</span> ";
    }
    else
    {
        $links[] = "<a href='{$report_def->get_url()}/edit?tab=$s'>".escape($section_title)."</a> ";            
    }    
}
echo implode(" &middot; ", $links);

echo "</div>";
?>

<form method='POST' action='<?php echo $report_def->get_url() ?>/save'>
<?php echo view('input/securitytoken'); ?>

<?php 
if (!$tab) { 
?>
    <!--
    <div class='input'>
        <label>Report Name</label><br />
    <?php    
        echo escape($report_def->get_handler()->get_title());
    ?>  
    </div>
    -->
    <div class='instructions' style='text-align:center'>
    <em>Note: To modify the report template, please contact Envaya.</em>
    </div>
    <hr />
    <?php
        //echo view('input/submit', array('value' => __('savechanges')));
        $report = new Report();
        $report->report_guid = $report_def->guid;
        $report->container_guid = Session::get_loggedin_user()->guid;
        
        echo $report->render_edit();
    ?>
    
    
<?php 
} 
else if ($tab == 'invite')
{
?>
<div class='padded'>
<p>To invite organizations to complete this report, send them this link:</p>

<div style='margin:10px;text-align:center;padding:10px;font-weight:bold;font-size:14px;border:1px solid gray;background-color:#f0f0f0'>
<a href='<?php echo $report_def->get_url(); ?>/start'><?php echo $report_def->get_url(); ?>/start</a>
</div>

<p>The organization then will need to provide their Envaya username and password to log in. If they do not yet have an Envaya account, 
they can register at that time.
</p>

<p>After the organization logs in to Envaya, you will be able to see them on the 
<a href='<?php echo $report_def->get_url(); ?>/edit?section=manage'>Manage Responses</a>
  page.
</p>


</div>
<?php
}
else if ($tab == 'manage')
{

    $reports = $report_def->query_reports()->filter();
    
    if ($reports)
    {
?>
<br />
<table class='gridTable'>
<tr class='header_row'>
    <th>Organization</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
<?php    

    foreach ($reports as $report)
    {
        $count += 1;
        $rowClass = (($count % 2) != 0) ? 'odd' : 'even';
        
        $org = $report->get_container_entity();
       
        echo "<tr class='$rowClass'>";
        echo "<td><a href='{$org->get_url()}'>".escape($org->name)."</a></td>";
        echo "<td>";        
        echo escape($report->get_status_text());        
        echo "</td>";        
        echo "<td style='white-space:nowrap'>";
        
        $status = $report->status;
        if ($status == ReportStatus::Blank)
        {
            echo "--";
        }
        else if ($status >= ReportStatus::Submitted)
        {
            echo "<a href='{$report->get_url()}/view_response'>".__("report:view_response")."</a><br />";   
        }
        echo view('reports/set_status_links', array('report' => $report));
        
        echo "</td>";
        echo "</tr>";
    }    
?>

</table>

<?php
    }
    else
    {
        echo "<div class='padded'>".__('report:no_responses')."</div>";
    }
}
else if ($tab == 'export')
{
    echo "<div class='padded'>";
    echo "<p>Export accepted responses as: ";
    echo "<a href='{$report_def->get_url()}?view=csv'>CSV</a>";
    echo "</p>";
    echo "</div>";
}
?>
</form>
</div>   
