<?php

$org = $vars['org'];

$lang = $org->language;

$website_url = abs_url($org->get_url());

echo sprintf(__('email:salutation', $lang), $org->name);
echo "\n\n";
echo __('register:approval_email:congratulations', $lang);
echo "\n";
echo $website_url;
echo "\n\n";
echo __('register:approval_email:nextsteps', $lang);
echo "\n\n";
echo __('register:approval_email:login', $lang);
echo "\n";
echo secure_url("/pg/login?username={$org->username}");
echo "\n\n";
echo sprintf(__('register:approval_email:share', $lang), $website_url);
echo "\n\n";
echo sprintf(__('register:approval_email:help', $lang), __('help', $lang));
echo "\n";
echo abs_url("/envaya/page/help");
echo "\n\n";
echo __('register:approval_email:thanks', $lang);
