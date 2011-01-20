<?php
$report_def = $vars['report_def'];
$reports = $report_def->query_approved()->filter();

$exported_fields = $report_def->get_exported_field_definitions();

/* 
 * figure out what columns need to appear in the result CSV 
 * (some fields may display their values in multiple columns)
 */

$column_name_sets = array();
$csv_rows = array();

foreach ($reports as $report)
{
    $csv_row = array();
    
    foreach ($exported_fields as $field_name => $field_def)
    {
        $field = $report->get_field($field_name);
                
        $exported_values = $field_def->get_exported_values($field);
                
        foreach ($exported_values as $csv_column => $csv_value)
        {
            $csv_row[$csv_column] = $csv_value;
            
            if (!isset($column_name_sets[$field_name]))
            {
                $column_name_sets[$field_name] = array(); // set of column names for that field name
            }            
            $column_name_sets[$field_name][$csv_column] = true;
        }
    }        
    $csv_rows[] = $csv_row;    
}

/* 
 *  generate a list of csv column names 
 *  in the same order as they appear in the report definition
 */

$column_names = array();
foreach ($column_name_sets as $field_name => $column_name_set)
{
    foreach ($column_name_set as $column_name => $v)
    {
        $column_names[] = $column_name;
    }
}

/*
 * generate the csv file
 */

$outstream = fopen("php://output",'w');  

fputcsv($outstream, $column_names, ',', '"');      

foreach ($csv_rows as $csv_row)
{
    $ordered_row = array();
    foreach ($column_names as $column_name)
    {
        $ordered_row[] = @$csv_row[$column_name];
    }

    fputcsv($outstream, $ordered_row, ',', '"');      
}

fclose($outstream);  