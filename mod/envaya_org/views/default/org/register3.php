<?php echo view("org/register_progress", array('current' => 3   )) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('register:homepage_instructions') ?>
</div>

<form action='<?php echo secure_url('/org/register3'); ?>' method='POST'>

<?php echo view('org/create_profile_form'); ?>

<div class='input'>
<label><?php echo __('register:homepage_label') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('register:homepage_button'),
    'track_dirty' => true
));
?>
</div>

</form>

</div>