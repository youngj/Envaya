<?php
    $email = $vars['email'];
    $user = $vars['user'];
?>

<b>From:</b> <?php echo escape($email->from) ?><br />
<b>Subject:</b> <?php echo escape($email->render_subject($user)) ?>
<br />
<br />
<iframe src='<?php echo $email->get_url() ?>/preview_body?user=<?php echo $user ? $user->guid : '' ?>' width='660' height='200'></iframe>