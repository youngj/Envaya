<div class='padded'>
<?php    
    $next = @$vars['next'];

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
    <?php echo view('input/submit', array('value' => __('login'))); ?>

    <div>
    <a href="<?php echo Config::get('url') ?>pg/forgot_password">
            <?php echo __('login:password:lost') ?>
    </a>    
    </div>

    <?php 
    
    echo view('focus', array('id' => $vars['username'] ? 'password' : 'username')); 
    
    if (@$vars['next'])
    {
        echo view('input/hidden', array(
            'name' => 'next',
            'value' => $vars['next']
        )); 
    }

    
    $form_body = ob_get_clean();

    echo view('input/form', array('body' => $form_body, 'action' => secure_url("/pg/login")));
?>

<br />
<p>
<?php echo sprintf(__('login:if_not_registered'), "<a href='/org/new' style='font-weight:bold'>".__('sign_up_now')."</a>"); ?>
</p>
<p>
<?php echo "<a href='/pg/register?next=".urlencode($next)."'>".__('login:register_link')."</a>"; ?>
</p>

</div>