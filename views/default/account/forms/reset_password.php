<?php

    $user = $vars['entity'];
?>

<form action='/pg/submit_password_reset' method='POST'>
<?php echo view('input/securitytoken'); ?>

<?php echo view('input/hidden', array('internalname' => 'u', 'value' => $user->guid)); ?>
<?php echo view('input/hidden', array('internalname' => 'c', 'value' => $user->passwd_conf_code)); ?>

<div class='input'>
<label><?php echo __('user:username:label') ?></label><br />
<?php echo $user->username; ?>
</div>

<div class='input'>
<label><?php echo __('user:password:new') ?></label><br />
<?php echo view('input/password', array(
    'internalname' => 'password'
)) ?>
<div class='help'><?php echo __('create:password:help') ?></div>
<div class='help' style='padding-top:5px'><?php echo __('create:password:length') ?></div>
</div>

<div class='input'>
<label><?php echo __('create:password2') ?></label><br />
<?php echo view('input/password', array(
    'internalname' => 'password2'
)) ?>
</div>

<?php echo view('input/submit', array('value' => __('login'))); ?>
</form>