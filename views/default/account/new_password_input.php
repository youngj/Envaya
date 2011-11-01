<?php
    $user = $vars['user'];
    echo view('js/password_strength');
?>
<div class='input'>
<label><?php echo __('user:password:new') ?>:</label><br />
<?php echo view('input/password', array(
    'id' => 'new_password',
    'name' => 'password',
)) ?>    
<div id='password_strength' style='height:2px;margin-left:4px;overflow:hidden'></div>
<script type='text/javascript'>
function updatePasswordStrength()
{
    setTimeout(function() {
        PasswordStrength.show(
            $('new_password').value, 
            <?php echo json_encode($user->get_easy_password_words()); ?>,
            <?php echo json_encode($user->get_min_password_strength()); ?>,
            $('password_strength')
        );
    }, 10);
}
addEvent($('new_password'), 'keypress', updatePasswordStrength);
addEvent($('new_password'), 'change', updatePasswordStrength);
</script>
<div class='help'><?php echo __('register:password:help').' '.__('register:password:remember'); ?></div>
<div class='help' style='padding-top:5px'><?php echo strtr(__('register:password:length'), array('{min}' => 6)); ?></div>
</div>
<div class='input'>
<label><?php echo __('user:password2:label') ?>:</label><br />
<?php echo view('input/password', array(
    'name' => 'password2'
)) ?>
</div>