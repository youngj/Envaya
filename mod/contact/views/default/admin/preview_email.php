<?php
    $email = $vars['email'];
    $user = $vars['user'];
?>

<b>From:</b> <span><?php echo escape($email->from) ?></span><br />
<b>Subject:</b> <span><?php echo escape($email->render_subject($user)) ?></span>
<br />
<br />
<iframe src='<?php echo $email->get_url() ?>/preview_body?user=<?php echo $user ? $user->guid : '' ?>' width='660' height='200'></iframe>