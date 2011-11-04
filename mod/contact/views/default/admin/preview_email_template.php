<?php
    $template = $vars['template'];
    $subscription = @$vars['subscription'];
?>
<b>From:</b> <span><?php echo escape($template->from) ?></span><br />
<b>Subject:</b> <span><?php echo escape($template->render_subject($subscription)) ?></span>
<br />
<br />
<iframe src='<?php echo $template->get_url() ?>/preview_body?subscription=<?php echo $subscription ? $subscription->guid : '' ?>' 
    width='660' height='200'></iframe>