<?php

     /**
     * Elgg register form
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd

     * @link http://elgg.org/
     */

    $username = get_input('u');
    $email = get_input('e');
    $name = get_input('n');

    $admin_option = false;
    if ((get_loggedin_user()->admin) && ($vars['show_admin']))
        $admin_option = true;

    $form_body = "<p><label>" . __('name') . "<br />" . elgg_view('input/text' , array('internalname' => 'name', 'class' => "general-textarea", 'value' => $name)) . "</label><br />";

    $form_body .= "<label>" . __('email') . "<br />" . elgg_view('input/text' , array('internalname' => 'email', 'class' => "general-textarea", 'value' => $email)) . "</label><br />";
    $form_body .= "<label>" . __('username') . "<br />" . elgg_view('input/text' , array('internalname' => 'username', 'class' => "general-textarea", 'value' => $username)) . "</label><br />";
    $form_body .= "<label>" . __('password') . "<br />" . elgg_view('input/password' , array('internalname' => 'password', 'class' => "general-textarea")) . "</label><br />";
    $form_body .= "<label>" . __('passwordagain') . "<br />" . elgg_view('input/password' , array('internalname' => 'password2', 'class' => "general-textarea")) . "</label><br />";

    // Add captcha hook
    $form_body .= elgg_view('input/captcha');

    if ($admin_option)
        $form_body .= elgg_view('input/checkboxes', array('internalname' => "admin", 'options' => array(__('admin_option'))));

    $form_body .= elgg_view('input/hidden', array('internalname' => 'friend_guid', 'value' => $vars['friend_guid']));
    $form_body .= elgg_view('input/hidden', array('internalname' => 'invitecode', 'value' => $vars['invitecode']));
    $form_body .= elgg_view('input/hidden', array('internalname' => 'action', 'value' => 'register'));
    $form_body .= elgg_view('input/submit', array('internalname' => 'submit', 'value' => __('register'))) . "</p>";
?>


    <?php echo elgg_view('input/form', array('action' => "pg/submit_registration", 'body' => $form_body)) ?>
