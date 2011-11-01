<div class='input'>
<label><?php echo __('register:user:name'); ?></label><br />
<?php echo view('input/text' , array('id' => 'name', 'name' => 'name')); ?>
</div>
<div class='input'>
<label><?php echo __('register:user:username'); ?></label><br />
<?php echo view('input/text' , array('id' => 'username', 'name' => 'username')); ?>
<div style='padding-top:5px' class='help'><?php echo strtr(__('register:username:help2'), array('{min}' => 6)); ?></div>
</div>

<?php
    echo view('focus', array('name' => 'name'));        
?>

<div class='input'>
<label><?php echo __('register:password') ?></label><br />
<?php echo view('input/password', array(
    'id' => 'password',
    'name' => 'password'
));

    echo view('js/password_strength');
 ?>
<div id='password_strength' style='height:2px;margin-left:4px;overflow:hidden'></div>
<script type='text/javascript'>
function updatePasswordStrength()
{
    setTimeout(function() {
        PasswordStrength.show(
            $('password').value, 
            [$('name').value, $('username').value, $('email').value, $('phone').value],
            PasswordStrength.VeryWeak,
            $('password_strength')
        );
    }, 10);
}
addEvent($('password'), 'keypress', updatePasswordStrength);
addEvent($('password'), 'change', updatePasswordStrength);
</script>

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
<?php echo view('input/text', array('id' => 'email', 'name' => 'email')); ?>
<div class='help'><?php echo __('register:user:email:help_2'); ?></div>
<div class='help'><?php echo __('register:user:email:help'); ?></div>
</div>

<div class='input'>
<label><?php echo __('register:user:phone') ?></label><br />
<?php echo view('input/text', array(
    'id' => 'phone',
    'name' => 'phone',
    'style' => "width:200px"
)) ?>
<div class='help'><?php echo __('register:phone:help') ?></div>
<div class='help'><?php echo __('register:phone:help_2') ?></div>
</div>
