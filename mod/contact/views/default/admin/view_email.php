<?php
    $user = $vars['user'];
    $email = $vars['email'];
?>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

<?php 
    echo view('admin/preview_email', array('email' => $email, 'user' => $user));
?>

<h3>Statistics</h3>

<ul>
<li>Number of emails sent: <?php echo $email->num_sent; ?></li>
<?php if ($email->time_last_sent) { ?>
<li>Last email sent: <?php echo friendly_time($email->time_last_sent); ?></li>
<?php } ?>
</ul>

<a href='<?php echo $email->get_url() ?>/send'><?php echo __('contact:send_email'); ?></a>

</div>
