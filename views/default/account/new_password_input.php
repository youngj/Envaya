<div class='input'>
<label><?php echo __('user:password:new') ?>:</label><br />
<?php echo view('input/password', array(
    'name' => 'password'
)) ?>
<div class='help'><?php echo __('register:password:help').' '.__('register:password:remember'); ?></div>
<div class='help' style='padding-top:5px'><?php echo strtr(__('register:password:length'), array('{min}' => 6)); ?></div>
</div>

<div class='input'>
<label><?php echo __('user:password2:label') ?>:</label><br />
<?php echo view('input/password', array(
    'name' => 'password2'
)) ?>
</div>