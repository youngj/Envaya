<?php
$section = $vars['section'];
$report = $vars['report'];

$title = __("{$report->get_handler()->report_code}:section{$section}_title");

echo "<h3>";

if ($report->can_edit())
{
    echo "<a style='float:right;font-weight:normal' href='{$report->get_edit_url()}?section={$section}'>".__('report:edit_section')."</a>";
}

echo escape($title);

echo "</h3>";

echo $vars['content'];

?>