<?php
$report = $vars['report'];
$section = $vars['section'];

$row = 0;

echo "<table class='gridTable' style='width:100%'>";

foreach ($section['field_names'] as $field_name)
{
    $row++;
    
    $row_class = ($row % 2) ? 'odd' : 'even';

    echo "<tr class='$row_class'><td>";
       
    $field = $report->get_field($field_name);
       
    echo $field->view_html();
    
    echo "</td><td style='width:130px;padding-top:8px'>";
    
    echo view('input/hidden', array(
        'name' => 'fields[]', 
        'value' => $field_name,
    ));    
    
    echo view('input/radio', array(
        'name' => $field_name, 
        'value' => $field->access,
        'options' => ReportAccess::get_options(),
        'trackDirty' => true
    ));
    
    echo "</td></tr>";
}

echo "</table>";

?>  