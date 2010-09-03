<?php
$report_def = $vars['report_def'];
$reports = $report_def->query_approved()->filter();

$field_names = array_keys($report_def->get_handler()->get_field_args());

$outstream = fopen("php://output",'w');  

fputcsv($outstream, $field_names, ',', '"');      

foreach ($reports as $report)
{
    $row = array();
    foreach ($field_names as $field_name)
    {
        $row[] = $report->get_field($field_name)->get_csv_value();
    }    
    fputcsv($outstream, $row, ',', '"');      
}

fclose($outstream);  
