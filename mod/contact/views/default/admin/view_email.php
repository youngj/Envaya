<?php
    $user = $vars['user'];
    $email = $vars['email'];
?>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>
<div style='padding-bottom:10px'>
<strong>Filters:</strong> (<?php echo $email->query_filtered_subscriptions()->count(); ?>/<?php 
echo EmailTemplate::query_all_subscriptions()->count(); ?></span> recipients in filter)
<?php
    foreach ($email->get_filters() as $filter)
    {
        echo "<div style='padding-left:60px'><strong>{$filter->get_name()}</strong>: {$filter->render_view()}</div>";
    }
?>
</div>
<?php 

    echo view('admin/preview_email', array('email' => $email, 'user' => $user));
    
    echo view('admin/email_statistics', array('email' => $email));
?>

<a href='<?php echo $email->get_url() ?>/send'><?php echo __('contact:send_email'); ?></a>

</div>
