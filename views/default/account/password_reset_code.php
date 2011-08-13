<div class='padded'>
<?php
    $user = $vars['user'];
?>
<form action='/pg/password_reset_code' method='POST'>
<?php echo view('input/securitytoken'); ?>
<?php echo view('input/hidden', array('name' => 'u', 'value' => $user->guid)); ?>
<?php echo __('user:password:reset_code_instructions'); ?>
<div class='input'>
<?php 
    echo view('input/text', array('name' => 'c', 'style' => 'width:200px'));
    echo view('focus', array('name' => 'c'));
?>
</div>
<?php
    echo view('input/submit', array('value' => __('submit'))); 
?>
</form>
</div>