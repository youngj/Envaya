<?php
    $user = Session::get_logged_in_user();

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
<label><?php echo __('message:name') ?></label><br />
<?php
   
    echo view('input/text', array(
        'name' => 'name',
        'value' => $user ? $user->name : Session::get('user_name')
    ));
?>
</div>
<div class='input'>
<label><?php echo __('message:email') ?></label><br />
<?php
    echo view('input/text', array(
        'name' => 'email',
        'value' => $user ? $user->email : Session::get('user_email')
    ));
?>
</div>

<?php
    echo view('input/submit', array(
        'value' => __('message:send'),
    ));
?>
