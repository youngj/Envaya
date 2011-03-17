<?php

$org = $vars['org'];

echo sprintf(__('email:salutation', $org->language), $org->name);
echo "\n\n";
echo __('email:orgapproved:congratulations', $org->language);
echo "\n";
echo $org->get_url();
echo "\n\n";
echo __('email:orgapproved:nextsteps', $org->language);
echo "\n\n";
echo __('email:orgapproved:login', $org->language);
echo "\n";
echo Config::get('url')."pg/login?username={$org->username}";
echo "\n\n";
echo sprintf(__('email:orgapproved:help', $org->language), __('help:title'), $org->language);
echo "\n";
echo "{$org->get_url()}/help";
echo "\n\n";
echo __('email:orgapproved:thanks', $org->language);
