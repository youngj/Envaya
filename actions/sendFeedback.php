<?php

$message = get_input('message');

if (!$message)
{
    register_error(elgg_echo('feedback:empty'));
    Session::saveInput();
    forward("page/contact");
}

$from = get_input('name');
$email = get_input('email');

$headers = array();

if ($email && is_email_address($email))
{
    $headers['Reply-To'] = mb_encode_mimeheader($email, "UTF-8", "B");
}

send_admin_mail("User feedback", "From: $from\n\nEmail: $email\n\n$message", $headers);
system_message(elgg_echo('feedback:sent'));
forward("page/contact");