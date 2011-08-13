<?php

$user = $vars['user'];
$code = $vars['code'];

echo sprintf(__('email:salutation', $user->language), $user->name);
echo "\n\n";
echo __('login:resetreq:somebody_requested', $user->language);
echo "\n\n";
echo __('login:resetreq:click_link', $user->language);
echo "\n";
echo secure_url("/pg/password_reset?u={$user->guid}&c={$code}");
echo "\n";