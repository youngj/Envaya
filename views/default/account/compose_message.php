<?php
    $to_user = $vars['user'];    
    $from_user = Session::get_logged_in_user();
?>

<div class='padded'>
<form action='<?php echo $to_user->get_url() ?>/send_message' method='POST'>

<?php echo view('input/securitytoken'); ?>

<table class='messageTable'>
<tr>
<th>
<?php echo __("message:from"); ?>
</th>
<td>
<strong><?php echo escape($from_user->name); ?></strong> &lt;<?php echo escape($from_user->email); ?>&gt;
</td>
</tr>
<tr>
<th>
<?php echo __("message:to"); ?>
</th>
<td>
<strong><?php echo escape($to_user->name); ?></strong> &lt;<?php echo escape($to_user->email); ?>&gt;
</td>
</tr>
<tr>
<th>
<?php echo __("message:subject"); ?>
</th>
<td>
<?php echo view('input/text', array('name' => 'subject')); ?>
</td>
</tr>
<tr>
<th>
<?php echo __("message:message"); ?>
</th>
<td>
<?php echo view('input/longtext', array('name' => 'message')); ?>
</td>

</tr>


</table>

<?php
echo view('input/submit',array(
    'value' => __('message:send')
));
?>

</form>
</div>