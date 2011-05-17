<div class='padded'>
<?php
    $user = $vars['user'];
?>

<form action='<?php echo $user->get_url(); ?>/password' method='POST'>
<?php echo view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo __('user:username:label') ?>:</label><br />
<?php echo $user->username; ?>
</div>

<?php if ($vars['require_old_password']) { ?>
<div class='input'>
<label><?php echo __('user:password:current') ?>:</label><br />
<?php echo view('input/password', array(
    'name' => 'old_password'
)) ?>
</div>
<?php echo view('focus', array('name' => 'old_password')); ?>
<?php } ?>
<?php echo view('account/new_password_input'); ?>
<?php echo view('input/submit', array('value' => __('savechanges'))); ?>
</form>
</div>