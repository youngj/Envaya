<?php

$org = $vars['org'];

$subscription = EmailSubscription_Contact::query_for_entity($org)->get();

if ($subscription)
{
    echo "<a href='/admin/contact/email/subscription/{$subscription->guid}'>".__('contact:send_email')."</a>";
}
