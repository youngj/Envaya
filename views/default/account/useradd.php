<?php
    $admin_option = false;
    if ((Session::get_loggedin_user()->admin) && ($vars['show_admin']))
        $admin_option = true;

    $form_body = "<p><label>" . __('name') . "<br />" . view('input/text' , array('name' => 'name')) . "</label></p>";

    $form_body .= "<p><label>" . __('email') . "<br />" . view('input/text' , array('name' => 'email')) . "</label></p>";
    $form_body .= "<p><label>" . __('username') . "<br />" . view('input/text' , array('name' => 'username')) . "</label></p>";
    $form_body .= "<p><label>" . __('password') . "<br />" . view('input/password' , array('name' => 'password')) . "</label></p>";
    $form_body .= "<p><label>" . __('passwordagain') . "<br />" . view('input/password' , array('name' => 'password2')) . "</label></p>";

    if ($admin_option)
        $form_body .= "<p>" . view('input/checkboxes', array('name' => "admin", 'options' => array(__('admin_option'))));

    $form_body .= view('input/submit', array('value' => __('register'))) . "</p>";
?>


    <div id="add-box">
    <h2><?php echo __('adduser'); ?></h2>
        <?php echo view('input/form', array('action' => Config::get('url')."admin/add_user", 'body' => $form_body)) ?>
    </div>