<?php
    $org = $vars['org'];
    $email = $vars['email'];
?>

<form action='/admin/send_email' method='POST'>

<div class='padded'>
<?php echo view('input/securitytoken'); ?>

Subject: <?php echo $email->render_subject($org) ?>
<br />
<br />
<iframe src='/admin/view_email_body?username=<?php echo $org ? $org->username : '' ?>&email=<?php echo $email->guid ?>' width='560' height='350'></iframe>

<?php

if ($org) 
{
    echo view('input/hidden',array(
        'name' => 'org_guid',
        'value' => $org->guid
    ));

    echo view('input/hidden',array(
        'name' => 'email',
        'value' => $email->guid
    ));

    echo view('input/hidden',array(
        'name' => 'from',
        'value' => $vars['from']
    ));


    echo view('input/submit',array(
        'value' => __('message:send')
    ));
}
?>

</div>
</form>