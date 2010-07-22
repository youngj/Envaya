<?php

     /**
     * Elgg login form
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd

     * @link http://elgg.org/
     */

    global $CONFIG;

    ob_start();
    ?>


    <label>
        <?php echo __('username') ?><br />
        <?php echo elgg_view('input/text', array(
            'internalname' => 'username',
            'internalid' => 'username',
            'value' => $vars['username'], 'class' => 'login-textarea')); ?>
    </label>
    <br />
    <label>
        <?php echo __('password') ?><br />
        <?php echo elgg_view('input/password', array(
            'internalname' => 'password',
            'internalid' => 'password',
            'class' => 'login-textarea')) ?>
    </label>
    <br />
    <div id="persistent_login"><label><input type="checkbox" class='input-checkboxes' name="persistent" value="true" />
        <?php echo __('user:persistent') ?>
    </label></div>
    <?php echo elgg_view('input/submit', array('value' => __('login'))); ?>
    <div>
        <a href="<?php echo $vars['url'] ?>pg/forgot_password">
        <?php echo __('user:password:lost') ?>
        </a>
    </div>

    <script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('<?php echo $vars['username'] ? 'password' : 'username' ?>').focus();
        }, 10);
    </script>
    <?php
    $form_body = ob_get_clean();

    $login_url = $vars['url'];
    if ((isset($CONFIG->https_login)) && ($CONFIG->https_login))
        $login_url = str_replace("http", "https", $vars['url']);
?>

        <?php
            echo elgg_view('input/form', array('body' => $form_body, 'action' => "{$login_url}pg/submit_login"));
        ?>
