<?php
    echo view('input/securitytoken');
?>
<div class='input'>
<div><label><?php echo __('message:message') ?></label></div>
<?php
    echo view('input/longtext', array(
        'name' => 'message',
        'track_dirty' => true,
    ));
?>
</div>
<div class='input'>
<label><?php echo __('message:name') ?></label>
<?php

    $name = Session::isloggedin() ? Session::get_loggedin_user()->name : Session::get('user_name');

    echo view('input/text', array(
        'name' => 'name',
        'value' => $name
    ));
?>
</div>
<div class='input'>
<label><?php echo __('message:email') ?></label>
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
        'value' => __('message:send'),
    ));
?>
