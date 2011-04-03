<div class='input'>
<div><label><?php echo __('feedback:message') ?>:</label></div>
<?php
    echo view('input/longtext', array(
        'name' => 'message',
        'trackDirty' => true,
    ));
?>
</div>
<div class='input'>
<label><?php echo __('feedback:name') ?>:</label>
<?php

    $name = Session::isloggedin() ? Session::get_loggedin_user()->name : Session::get('user_name');

    echo view('input/text', array(
        'name' => 'name',
        'value' => $name
    ));
?>
</div>
<div class='input'>
<label><?php echo __('feedback:email') ?>:</label>
<?php
    $email = Session::isloggedin() ? Session::get_loggedin_user()->email : '';

    echo view('input/text', array(
        'name' => 'email',
        'value' => $email
    ));
?>
</div>

<?php
    echo view('input/submit', array(
        'value' => __('feedback:send'),
    ));
?>
