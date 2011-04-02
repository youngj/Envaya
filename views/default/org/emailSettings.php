<?php
    $email = $vars['email'];
    $users = $vars['users'];
    $code = get_email_fingerprint($email);

?>
<form action='/org/emailSettings' method='POST'>

<div class='input'>

<?php
    echo view('input/hidden', array(
        'name' => 'email',
        'value' => $email
    ));

    echo view('input/hidden', array(
        'name' => 'code',
        'value' => $code
    ));

?>

    <div class='help'>
    <?php echo sprintf(__('user:notification:desc2'), "<em>".escape($email)."</em>"); ?>        
    </div>
	<?php

        echo view("input/checkboxes", array('name' => 'notifications', 
			'value' => $users[0]->get_notifications(), 
			'options' => Notification::get_options()
        ));

     ?>

</div>

<?php

echo view('input/submit',array(
    'value' => __('savechanges')
));
?>

</form>