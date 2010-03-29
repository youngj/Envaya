<?php

    $org = $vars['entity'];
    $user = $vars['user'];
    
?>

<div class='padded'>
<form action='action/org/sendMessage' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<table class='messageTable'>
<tr>
<th>
<?php echo elgg_echo("message:from"); ?>
</th>
<td>
<strong><?php echo escape($user->name); ?></strong> &lt;<?php echo escape($user->email); ?>&gt;
</td>
</tr>
<tr>
<th>
<?php echo elgg_echo("message:to"); ?>  
</th>
<td>
<strong><?php echo escape($org->name); ?></strong> &lt;<?php echo escape($org->email); ?>&gt;
</td>
</tr>
<tr>
<th>
<?php echo elgg_echo("message:subject"); ?>
</th>
<td>
<?php echo elgg_view('input/text', array('internalname' => 'subject')); ?>
</td>
</tr>
<tr>
<th>
<?php echo elgg_echo("message:message"); ?>
</th>
<td>
<?php echo elgg_view('input/longtext', array('internalname' => 'message')); ?>
</td>

</tr>


</table>

<?php 
echo elgg_view('input/hidden', array('internalname' => 'recipient_guid', 'value' => $org->guid));

echo elgg_view('input/submit',array(
    'value' => elgg_echo('message:send')
));
?>

</form>
</div>