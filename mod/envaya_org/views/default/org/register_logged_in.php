<div class='section_content padded'>

<?php
    $user = Session::get_logged_in_user();
?>
<p>
<?php echo strtr(__('register:already_logged_in'), array('{name}' => escape($user->name))); ?>
</p>

<p>
<?php echo __('register:must_log_out'); ?>
</p>

<form method='POST' action='/org/register_logged_in'>
<?php echo view('input/securitytoken'); ?>

<div style='float:right'>
<br />
<a href='<?php echo $user->get_url() ?>'><?php echo __('cancel'); ?></a>
</div>

<?php 
    echo view('input/submit', array(
        'value' => __('logout')
    )); 
?>


</form>
</div>