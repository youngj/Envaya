<?php
    admin_gatekeeper();
    action_gatekeeper();

    $orgs = Organization::filterByCondition(
        array("approval > 0 AND notify_days > 0 AND ((last_notify_time IS NULL) OR (last_notify_time + notify_days * 86400 < ?)) AND email <> ''"),
        array($time), '', 2);

    global $CONFIG;

    if (sizeof($orgs) > 0)
    {
        foreach ($orgs as $org)
        {
            $nag = '';
            $relatedUpdates = '';

            $subject = sprintf(elgg_echo('email:updates:subject', $org->language));

            $body = sprintf(elgg_echo('email:updates:body', $org->language),
                $org->name,
                $nag,
                $relatedUpdates,
                "{$CONFIG->url}{$org->username}/feed",
                "{$CONFIG->url}org/emailSettings?e=".urlencode($org->email)."&c=".get_email_fingerprint($org->email)
            );

            notify_user($org->guid, null, $subject, $body,NULL, 'email');

            $org->last_notify_time = time();
            $org->save();

            system_message(elgg_echo('email:updates:sent'));
        }
    }
    else
    {
        register_error(elgg_echo('email:updates:none'));
    }

    forward_to_referrer();