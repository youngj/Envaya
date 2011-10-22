<?php
    $email = $vars['email'];
    $subscription = $vars['subscription'];
?>

<b>From:</b> <span><?php echo escape($email->from) ?></span><br />
<b>Subject:</b> <span><?php echo escape($email->render_subject($subscription)) ?></span>
<br />
<br />
<iframe src='<?php echo $email->get_url() ?>/preview_body?subscription=<?php echo $subscription ? $subscription->guid : '' ?>' width='660' height='200'></iframe>