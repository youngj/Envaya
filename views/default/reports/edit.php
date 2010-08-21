<div class='section_content padded'>

<?php
$report = $vars['report'];
?>
<form method='POST' action='<?php echo $report->getURL()."/save" ?>'>
<?php
echo view('input/securitytoken'); 
echo $report->renderEdit();
?>

<?php echo view('input/submit', array('value' => __('save'), 'trackDirty' => true)); ?>
</form>
</div>