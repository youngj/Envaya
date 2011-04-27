<div class='padded'>
<?php

    $user = $vars['entity'];
?>

<form action='/pg/password_reset' method='POST'>
<?php echo view('input/securitytoken'); ?>

<?php echo view('input/hidden', array('name' => 'u', 'value' => $user->guid)); ?>
<?php echo view('input/hidden', array('name' => 'c', 'value' => $user->get_metadata('passwd_conf_code'))); ?>

<div class='input'>
<label><?php echo __('user:username:label') ?></label><br />
<?php echo $user->username; ?>
</div>

<div class='input'>
<label><?php echo __('user:password:new') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password'
)) ?>
<div class='help'><?php echo __('register:password:help') ?></div>
<div class='help' style='padding-top:5px'><?php echo __('register:password:length') ?></div>
</div>

<div class='input'>
<label><?php echo __('register:password2') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password2'
)) ?>
</div>

<?php echo view('input/submit', array('value' => __('login'))); ?>
</form>
</div>