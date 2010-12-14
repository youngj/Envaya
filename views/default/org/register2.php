<?php echo view("org/registerProgress", array('current' => 2)) ?>

<div class='padded'>
<div id='instructions'>
    <?php echo __('create:instructions') ?>
</div>

<form action='<?php echo $vars['config']->secure_url; ?>org/register2' method='POST'>
<?php
    echo view('org/create_account_form');
?>
</form>

</div>