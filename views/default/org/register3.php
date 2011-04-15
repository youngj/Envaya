<?php echo view("org/register_progress", array('current' => 3   )) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('setup:instructions') ?>
</div>

<form action='<?php echo Config::get('secure_url'); ?>org/register3' method='POST'>

<?php echo view('org/create_profile_form'); ?>

<div class='input'>
<label><?php echo __('setup:next') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('setup:next:button'),
    'trackDirty' => true
));
?>
</div>

</form>

</div>