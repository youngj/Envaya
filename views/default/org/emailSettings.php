<?php
    $email = $vars['email'];
    $users = $vars['users'];
    $code = get_email_fingerprint($email);

?>
<form action='org/emailSettings_save' method='POST'>

<div class='instructions'>
<?php echo sprintf(__('user:notification:desc'), "<em>".escape($email)."</em>"); ?>
</div>

<div class='input'>

<?php
    echo view('input/hidden', array(
        'internalname' => 'email',
        'value' => $email
    ));

    echo view('input/hidden', array(
        'internalname' => 'code',
        'value' => $code
    ));

?>

    <div class='help'><?php echo __('user:notification:freq'); ?>:
    <?php

        echo view("input/pulldown", array('internalname' => 'notify_days', 'value' => $users[0]->notify_days, 'options_values' =>
            get_notification_frequencies()
        ));

     ?></div>

</div>

<?php

echo view('input/submit',array(
    'value' => __('savechanges')
));
?>

</form>