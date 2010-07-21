<?php
    admin_gatekeeper();
    action_gatekeeper();

    /*
    $orgs = Organization::filterByCondition(
        array("approval > 0 AND notify_days > 0 AND ((last_notify_time IS NULL) OR (last_notify_time + notify_days * 86400 < ?)) AND email <> ''"),
        array($time), '', 1);
    */

    global $CONFIG;

    $org = get_entity(get_input('org_guid'));

    if ($org && $org->email && $org->notify_days > 0 && $org->approval > 0
        && (!$org->last_notify_time || $org->last_notify_time + $org->notify_days * 86400 < time())
        )
    {
        $subject = elgg_echo('email:reminder:subject', $org->language);

        $body = elgg_view('emails/reminder', array('org' => $org));

        $headers = array(
            'To' => $org->getNameForEmail(),
            'Content-Type' => 'text/html'
        );

        send_mail($org->email, $subject, $body, $headers);

        $org->last_notify_time = time();
        $org->save();

        system_message(elgg_echo('email:reminder:sent'));
    }
    else
    {
        register_error(elgg_echo('email:reminder:none'));
    }

    forward(get_input('from') ?: "/admin/contact");