<?php
    $org = $vars['org'];
?>

<form action='admin/send_email' method='POST'>

<div class='padded'>
<?php echo elgg_view('input/securitytoken'); ?>

Subject: <?php echo __('email:reminder:subject', $org->language) ?>
<br />
<br />
<iframe src='admin/view_email?username=<?php echo $org->username ?>' width='560' height='350'></iframe>

<?php

echo elgg_view('input/hidden',array(
    'internalname' => 'org_guid',
    'value' => $org->guid
));

echo elgg_view('input/hidden',array(
    'internalname' => 'from',
    'value' => $vars['from']
));


echo elgg_view('input/submit',array(
    'value' => __('message:send')
));
?>
</div>
</form>