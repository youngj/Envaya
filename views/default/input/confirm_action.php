<form method='POST' action='<?php echo escape($vars['ok_url']); ?>'>
<?php echo view('input/securitytoken'); ?>



<?php 
    echo view('input/submit', array(
        'value' => __('ok')
    ));    
?>
</form>

<form method='GET' action='<?php echo escape($vars['cancel_url']); ?>'>
<?php 
    echo view('input/submit', array(
        'value' => __('cancel')
    ));    
?>
</form>
