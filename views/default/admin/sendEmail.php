<?php
    $org = $vars['org'];
?>

<form action='admin/send_email' method='POST'>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

Subject: <?php echo __('email:reminder:subject', $org->language) ?>
<br />
<br />
<iframe src='admin/view_email?username=<?php echo $org->username ?>' width='560' height='350'></iframe>

<?php

echo view('input/hidden',array(
    'internalname' => 'org_guid',
    'value' => $org->guid
));

echo view('input/hidden',array(
    'internalname' => 'from',
    'value' => $vars['from']
));


echo view('input/submit',array(
    'value' => __('message:send')
));
?>
</div>
</form>