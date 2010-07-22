<?php
    /**
     * Elgg add user form.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    $admin_option = false;
    if ((get_loggedin_user()->admin) && ($vars['show_admin']))
        $admin_option = true;

    $form_body = "<p><label>" . __('name') . "<br />" . elgg_view('input/text' , array('internalname' => 'name')) . "</label></p>";

    $form_body .= "<p><label>" . __('email') . "<br />" . elgg_view('input/text' , array('internalname' => 'email')) . "</label></p>";
    $form_body .= "<p><label>" . __('username') . "<br />" . elgg_view('input/text' , array('internalname' => 'username')) . "</label></p>";
    $form_body .= "<p><label>" . __('password') . "<br />" . elgg_view('input/password' , array('internalname' => 'password')) . "</label></p>";
    $form_body .= "<p><label>" . __('passwordagain') . "<br />" . elgg_view('input/password' , array('internalname' => 'password2')) . "</label></p>";

    if ($admin_option)
        $form_body .= "<p>" . elgg_view('input/checkboxes', array('internalname' => "admin", 'options' => array(__('admin_option'))));

    $form_body .= elgg_view('input/submit', array('internalname' => 'submit', 'value' => __('register'))) . "</p>";
?>


    <div id="add-box">
    <h2><?php echo __('adduser'); ?></h2>
        <?php echo elgg_view('input/form', array('action' => "{$vars['url']}admin/add_user", 'body' => $form_body)) ?>
    </div>