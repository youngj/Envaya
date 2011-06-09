<?php echo view("org/register_progress", array('current' => 2)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('register:account_instructions') ?>
</div>

<form action='<?php echo secure_url('/org/register2'); ?>' method='POST'>
<?php
    echo view('org/create_account_form');
?>
</form>

</div>