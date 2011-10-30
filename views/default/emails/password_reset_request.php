<?php

$user = $vars['user'];
$code = $vars['code'];

$lang = $user->language;

echo sprintf(__('email:salutation', $lang), $user->name);
echo "\n\n";
echo __('login:resetreq:somebody_requested', $lang);
echo "\n\n";
echo __('login:resetreq:click_link', $lang);
echo "\n";
echo secure_url("/pg/password_reset?u={$user->guid}&c={$code}");
echo "\n";
