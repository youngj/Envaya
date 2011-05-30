<?php
    $org = $vars['org'];
    $email = $vars['email'];
?>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

<?php 
    echo view('admin/preview_email', array('email' => $email, 'org' => $org));
?>

<a href='<?php echo $email->get_url() ?>/send'>Send batch email</a>

</div>
