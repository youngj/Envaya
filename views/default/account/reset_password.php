<div class='padded'>
<?php

    $user = $vars['entity'];
?>

<form action='/pg/password_reset' method='POST'>
<?php echo view('input/securitytoken'); ?>

<?php echo view('input/hidden', array('name' => 'u', 'value' => $user->guid)); ?>
<?php echo view('input/hidden', array('name' => 'c', 'value' => $user->get_metadata('passwd_conf_code'))); ?>

<div class='input'>
<label><?php echo __('user:username:label') ?>:</label><br />
<?php echo $user->username; ?>
</div>

<?php 
    echo view('account/new_password_input'); 
    echo view('focus', array('name' => 'password'));
?>

<?php echo view('input/submit', array('value' => __('login'))); ?>
</form>
</div>