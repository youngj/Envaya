<?php
    $email = $vars['email'];
    $users = $vars['users'];
    $code = get_email_fingerprint($email);

?>
<form action='/org/emailSettings_save' method='POST'>

<div class='instructions'>
<?php echo __('user:notification:desc'); ?>
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

    <div class='help'>
    <?php echo sprintf(__('user:notification:desc2'), "<em>".escape($email)."</em>"); ?>        
    <?php

        echo view("input/pulldown", array('internalname' => 'enable_batch_email', 'value' => $users[0]->enable_batch_email, 'options_values' =>
            get_batch_email_options()
        ));

     ?></div>

</div>

<?php

echo view('input/submit',array(
    'value' => __('savechanges')
));
?>

</form>