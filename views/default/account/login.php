<div class='section_content padded'>
<?php    
    ob_start();
    ?>

    <label>
        <?php echo __('username') ?>    </label><br />
        <?php echo view('input/text', array(
            'name' => 'username',
            'id' => 'username',
            'value' => $vars['username'], 'class' => 'input-text login-input')); ?>
    <br />
    <label>
        <?php echo __('password') ?></label><br />
        <?php echo view('input/password', array(
            'name' => 'password',
            'id' => 'password',
            'class' => 'input-text login-input')) ?>
    <br />
    <div id="persistent_login"><label><input type="checkbox" class='input-checkboxes' name="persistent" value="true" />
        <?php echo __('login:persistent') ?>
    </label></div>
    <?php 
    
    echo view('input/submit', array('value' => __('login')));     
    
    echo view('focus', array('id' => $vars['username'] ? 'password' : 'username')); 
    
    if (@$vars['next'])
    {
        echo view('input/hidden', array(
            'name' => 'next',
            'value' => $vars['next']
        )); 
    }
    
    $form_body = ob_get_clean();

    echo view('input/form', array('body' => $form_body, 'action' => secure_url("/pg/login", Request::get_host())));
    
    echo view('account/login_links', $vars);
?>
</div>