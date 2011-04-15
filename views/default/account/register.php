<?php     

    $username = get_input('u');
    $email = get_input('e');
    $name = get_input('n');

    $admin_option = false;
    if ((Session::get_loggedin_user()->admin) && ($vars['show_admin']))
        $admin_option = true;

    $form_body = "<p><label>" . __('name') . "<br />" . view('input/text' , array('name' => 'name', 'class' => "general-textarea", 'value' => $name)) . "</label><br />";

    $form_body .= "<label>" . __('email') . "<br />" . view('input/text' , array('name' => 'email', 'class' => "general-textarea", 'value' => $email)) . "</label><br />";
    $form_body .= "<label>" . __('username') . "<br />" . view('input/text' , array('name' => 'username', 'class' => "general-textarea", 'value' => $username)) . "</label><br />";
    $form_body .= "<label>" . __('password') . "<br />" . view('input/password' , array('name' => 'password', 'class' => "general-textarea")) . "</label><br />";
    $form_body .= "<label>" . __('passwordagain') . "<br />" . view('input/password' , array('name' => 'password2', 'class' => "general-textarea")) . "</label><br />";

    if ($admin_option)
        $form_body .= view('input/checkboxes', array('name' => "admin", 'options' => array(__('admin_option'))));

    $form_body .= view('input/hidden', array('name' => 'friend_guid', 'value' => $vars['friend_guid']));
    $form_body .= view('input/hidden', array('name' => 'invitecode', 'value' => $vars['invitecode']));
    $form_body .= view('input/hidden', array('name' => 'action', 'value' => 'register'));
    $form_body .= view('input/submit', array('value' => __('register'))) . "</p>";
?>


    <?php echo view('input/form', array('action' => "pg/submit_registration", 'body' => $form_body)) ?>
