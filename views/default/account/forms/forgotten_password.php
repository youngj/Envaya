<div class='padded'>
<?php
    $form_body = "<p>" . __('user:password:text') . "</p>";
    $form_body .= "<p><label>". __('user:username_or_email') . " " . 
        view('input/text', array('internalname' => 'username', 'value' => $vars['username'])) . 
        "</label></p>";
    $form_body .= "<p>" . view('input/submit', array('value' => __('submit_request'))) . "</p>";

    echo view('input/form', array('action' => Config::get('url')."pg/request_new_password", 'body' => $form_body)); 
?>
</div>