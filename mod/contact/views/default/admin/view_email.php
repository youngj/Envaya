<?php
    $user = $vars['user'];
    $email = $vars['email'];
?>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

<?php 
    echo view('admin/preview_email', array('email' => $email, 'user' => $user));
    
    echo view('admin/email_statistics', array('email' => $email));
?>

<a href='<?php echo $email->get_url() ?>/send'><?php echo __('contact:send_email'); ?></a>

</div>
