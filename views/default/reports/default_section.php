<?php
$report = $vars['report'];
$html = @$vars['edit'] ? 'edit_html' : 'view_html';
$section = $vars['section'];

foreach ($section['field_names'] as $field_name)
{
    echo $report->get_field($field_name)->$html();
}

?>  