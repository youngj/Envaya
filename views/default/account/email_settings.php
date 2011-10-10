<div class='section_content padded'>
<?php
    $email = $vars['email'];
    $users = $vars['users'];
    $notification_type = $vars['notification_type'];
    $code = User::get_email_fingerprint($email);

?>
<form action='/pg/email_settings' method='POST'>


<div class='input'>

<?php
    echo view('input/securitytoken');

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

        if ($notification_type)
        {
            echo view('input/hidden', array(
                'name' => 'notification_type',
                'value' => $notification_type
            ));        
            
            $options = Notification::get_options();
            
            echo view("input/checkboxes", array('name' => 'notifications', 
                'value' => $users[0]->get_notifications(), 
                'options' => array(
                    $notification_type => $options[$notification_type]
                )
            ));                        
        }
        else
        {
            echo view("input/checkboxes", array('name' => 'notifications', 
                'value' => $users[0]->get_notifications(), 
                'options' => Notification::get_options()
            ));
        }

     ?>

</div>

<?php

echo view('input/submit',array(
    'value' => __('savechanges')
));

if ($notification_type)
{
    echo "<div style='float:right;padding-top:22px;font-size:11px'><a href='{$users[0]->get_email_settings_url()}'>".__('user:notification:all')."</a></div>";
}

?>

</form>
</div>