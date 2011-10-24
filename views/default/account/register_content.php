<div class='input'>
<label><?php echo __('register:user:name'); ?></label><br />
<?php echo view('input/text' , array('name' => 'name')); ?>
</div>
<div class='input'>
<label><?php echo __('register:user:username'); ?></label><br />
<?php echo view('input/text' , array('name' => 'username')); ?>
<div style='padding-top:5px' class='help'><?php echo strtr(__('register:username:help2'), array('{min}' => 6)); ?></div>
</div>

<?php
    echo view('focus', array('name' => 'name'));        
?>

<div class='input'>
<label><?php echo __('register:password') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password'
)) ?>
<div class='help'><?php echo __('register:user:password:help').' '.__('register:password:remember'); ?></div>
<div class='help' style='padding-top:5px'><?php echo strtr(__('register:password:length'), array('{min}' => 6)); ?></div>
</div>

<div class='input'>
<label><?php echo __('register:password2') ?></label><br />
<?php echo view('input/password', array(
    'name' => 'password2'
)) ?>
</div>

<div class='input'>
<label><?php echo __('register:user:email'); ?></label><br />
<?php echo view('input/text', array('name' => 'email')); ?>
<div class='help'><?php echo __('register:user:email:help_2'); ?></div>
<div class='help'><?php echo __('register:user:email:help'); ?></div>
</div>

<div class='input'>
<label><?php echo __('register:user:phone') ?></label><br />
<?php echo view('input/text', array(
    'name' => 'phone',
    'style' => "width:200px"
)) ?>
<div class='help'><?php echo __('register:phone:help') ?></div>
<div class='help'><?php echo __('register:phone:help_2') ?></div>
</div>
