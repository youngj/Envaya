<?php
    $org = $vars['org'];
    $email = $vars['email'];
?>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

<?php 
    echo view('admin/preview_email', array('email' => $email, 'org' => $org));
?>

<a href='/admin/send_email?email=<?php echo $email->guid ?>'>Send batch email</a>

</div>
