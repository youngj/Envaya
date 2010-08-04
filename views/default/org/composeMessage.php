<?php

    $org = $vars['entity'];
    $user = $vars['user'];

?>

<div class='padded'>
<form action='<?php echo $org->getURL() ?>/send_message' method='POST'>

<?php echo view('input/securitytoken'); ?>

<table class='messageTable'>
<tr>
<th>
<?php echo __("message:from"); ?>
</th>
<td>
<strong><?php echo escape($user->name); ?></strong> &lt;<?php echo escape($user->email); ?>&gt;
</td>
</tr>
<tr>
<th>
<?php echo __("message:to"); ?>
</th>
<td>
<strong><?php echo escape($org->name); ?></strong> &lt;<?php echo escape($org->email); ?>&gt;
</td>
</tr>
<tr>
<th>
<?php echo __("message:subject"); ?>
</th>
<td>
<?php echo view('input/text', array('internalname' => 'subject')); ?>
</td>
</tr>
<tr>
<th>
<?php echo __("message:message"); ?>
</th>
<td>
<?php echo view('input/longtext', array('internalname' => 'message')); ?>
</td>

</tr>


</table>

<?php
echo view('input/hidden', array('internalname' => 'recipient_guid', 'value' => $org->guid));

echo view('input/submit',array(
    'value' => __('message:send')
));
?>

</form>
</div>