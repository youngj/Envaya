<?php
    $org = $vars['org'];
    $email = $vars['email'];
?>

<form action='/admin/send_email' method='POST'>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

<?php 
    echo view('admin/preview_email', array('email' => $email, 'org' => $org));
?>

<a  style='font-size:10px;' href='/admin/batch_email?email=<?php echo $email->guid ?>'>Send batch email</a>

</div>
</form>
