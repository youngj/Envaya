<?php
    $email = $vars['email'];
    $org = $vars['org'];
?>

<b>From:</b> <?php echo escape($email->from) ?><br />
<b>Subject:</b> <?php echo escape($email->render_subject($org)) ?>
<br />
<br />
<iframe src='/admin/view_email_body?username=<?php echo $org ? $org->username : '' ?>&email=<?php echo $email->guid ?>' width='560' height='200'></iframe>
<a style='font-size:10px;float:right' href='/admin/edit_email?email=<?php echo $email->guid ?>'>Edit email template</a><br />