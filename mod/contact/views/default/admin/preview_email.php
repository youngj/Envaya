<?php
    $email = $vars['email'];
    $org = $vars['org'];
?>

<b>From:</b> <?php echo escape($email->from) ?><br />
<b>Subject:</b> <?php echo escape($email->render_subject($org)) ?>
<br />
<br />
<iframe src='<?php echo $email->get_url() ?>/preview_body?username=<?php echo $org ? $org->username : '' ?>' width='660' height='200'></iframe>