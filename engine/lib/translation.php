<?php

class TranslateMode
{
    const None = 1;
    const ManualOnly = 2;
    const All = 3;
}

function get_translate_mode()
{
    return ((int)get_input("trans")) ?: TranslateMode::ManualOnly;
}

function get_translations_url($translations, $targetLang = null)
{
    foreach ($translations as $trans)
    {
        $urlProps[] = "prop[]={$trans->container_guid}.{$trans->property}.{$trans->html}";
    }
    $urlProps[] = "targetLang=".($targetLang ?: get_language());

    $escUrl = urlencode($_SERVER['REQUEST_URI']);
    return "/org/translate?from=$escUrl&".implode("&", $urlProps);
}

function translate_listener($event, $object_type, $translation)
{
    PageContext::add_available_translation($translation);
}

register_event_handler('translate','all','translate_listener');