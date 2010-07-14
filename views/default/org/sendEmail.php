<?php
    $org = $vars['org'];
?>

<form action='action/org/sendEmail' method='POST'>

<div class='padded'>
<?php echo elgg_view('input/securitytoken'); ?>

Subject: <?php echo elgg_echo('email:reminder:subject', $org->language) ?>
</div>
<iframe src='org/viewEmail?username=<?php echo $org->username ?>' width='580' height='400'></iframe>

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
    'value' => elgg_echo('message:send')
));
?>

</form>