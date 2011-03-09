<?php
$report = $vars['report'];
$report_def = $report->get_report_definition();
?>

<div class='section_content padded report_view'>

<form method='POST' action='<?php echo $report->get_url() ?>/access_settings'>
<?php echo view('input/securitytoken'); ?>
<div class='report_preview_message'>
<?php echo sprintf(__('report:access_settings_instructions'), 
    escape($report_def->get_container_entity()->name), 
    "<em>".__('report:save_changes')."</em>"); ?>
</div>

<?php
echo $report->render_view(
    array('section_view' => 'reports/section_access')
);
?>

<?php

echo view('input/submit', array(
    'value' => __('report:save_changes'), 
    'trackDirty' => true
));
?>

</form>

</div>
