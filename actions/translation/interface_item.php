<?php

$key = get_input('key');
$value = get_input('value');
$lang = 'sw';

$trans = InterfaceTranslation::getByKeyAndLang($key, $lang);

if (!$trans)
{
    $trans = new InterfaceTranslation();
    $trans->key = $key;
    $trans->lang = $lang;
}

$trans->approval = 0;
$trans->owner_guid = get_loggedin_userid();
$trans->value = $value;
$trans->save();

system_message(elgg_echo("trans:posted"));

forward(get_input('from'));