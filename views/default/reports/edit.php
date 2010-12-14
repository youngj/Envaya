<div class='section_content padded'>
<?php
$report = $vars['report'];
$start = $vars['start'];

if ($start) {
?>

<div class='report_preview_message'>
    <p><?php echo __('report:start_message'); ?></p>
    <p class='last-paragraph'><?php echo __('report:start_message_2'); ?></p>
</div>

<?php
}
?>

<form method='POST' action='<?php echo $report->get_url()."/save" ?>'>
<?php
echo view('input/securitytoken'); 
echo $report->render_edit();
?>
</form>
</div>