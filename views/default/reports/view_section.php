<?php
$section_id = $vars['section_id'];
$section = $vars['section'];
$report = $vars['report'];

echo "<h3>";

if ($report->can_edit())
{
    echo "<a style='float:right;font-weight:normal' href='{$report->get_edit_url()}?section={$section_id}'>".__('report:edit_section')."</a>";
}

echo escape($section['title']);

echo "</h3>";

echo $vars['content'];

?>