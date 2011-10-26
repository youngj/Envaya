<?php
    $template = $vars['template'];
    $subscription = @$vars['subscription'];

    if (!$subscription)
    {
        $subscription = new SMSSubscription_Contact();
        $subscription->email = '{{email}}';
    }
        
    $content = $template->render_content($subscription);        
    echo nl2br(escape($content));
    echo "<div>(".strlen($content)." characters)</div>";