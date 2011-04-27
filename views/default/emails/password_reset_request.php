<?php

$user = $vars['user'];

echo sprintf(__('email:salutation', $user->language), $user->name);
echo "\n\n";
echo __('login:resetreq:somebody_requested', $user->language);
echo "\n\n";
echo __('login:resetreq:click_link', $user->language);
echo "\n";
$code = $user->get_metadata('passwd_conf_code');
echo Config::get('secure_url') . "pg/password_reset?u={$user->guid}&c={$code}";
echo "\n";