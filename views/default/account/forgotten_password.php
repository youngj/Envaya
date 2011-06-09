<div class='padded'>
<?php
    $form_body = "<p>" . __('login:resetreq:text') . "</p>";
    $form_body .= "<p><label>". __('login:username_or_email') . " " . 
        view('input/text', array('name' => 'username', 'value' => $vars['username'])) . 
        "</label></p>";
    $form_body .= "<p>" . view('input/submit', array('value' => __('login:resetreq:submit'))) . "</p>";

    echo view('focus', array('name' => 'username'));
    echo view('input/form', array('action' => "/pg/forgot_password", 'body' => $form_body)); 
?>
</div>