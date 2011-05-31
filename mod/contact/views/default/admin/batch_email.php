<div class='padded'>
<?php 
    $users = $vars['users'];
    $email = $vars['email'];
    
    $user = $users[0];
    if ($user) {
?>

<form action='<?php echo $email->get_url() ?>/send' method='POST'>

<?php echo view('input/securitytoken'); ?>
To:
<div style='<?php echo (sizeof($users) > 5) ? "height:150px;overflow:auto;" : ''; ?>font-size:10px'>
<?php
    
    $options = array();
    foreach ($users as $user)
    {
        $options[$user->guid] = $user->get_name_for_email();
    }

 echo view('input/checkboxes', array(
    'name' => 'users',
    'options' => $options,
    'value' => array_keys($options)
 ));
?>
</div>
<br />

<?php 
    echo view('admin/preview_email', array('email' => $email, 'user' => $user));
?>

<?php
    echo view('input/hidden',array(
        'name' => 'from',
        'value' => get_input('from')
    ));
    
    echo view('input/submit',array(
        'value' => __('message:send')
    ));

    }
?>
</div>