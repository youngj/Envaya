<?php
    $report_def = $vars['report_def'];
?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('report:create_profile_instructions') ?>
</div>

<form action='<?php echo secure_url($report_def->get_url()."/new_profile") ?>' method='POST'>
<?php
    echo view('org/create_profile_form');
?>

<div class='input'>
<label><?php echo __('report:create_profile:next') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('report:create_profile:button'),
    'trackDirty' => true
));
?>
</div>

</form>

</div>