<?php

function init_languages()
{
    foreach (Config::get('languages') as $code => $lang_name)
    {
        Language::init($code)->add_translations(
            array($code => $lang_name)
        );
    }
}

function get_accept_language()
{
    $acceptLanguage = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if ($acceptLanguage)
    {
        $languages = explode(",", $acceptLanguage);
        foreach ($languages as $language)
        {
            $langQ = explode(";", $language);
            $lang = trim($langQ[0]);
            $langLocale = explode("-", $lang);
            return $langLocale[0];
        }
    }
}

/**
* Gets the current language in use by the system or user.
* @return string The language code (eg "en")
*/
function get_language()
{
    $language = '';

    global $CURRENT_LANGUAGE;
    if ($CURRENT_LANGUAGE)
    {
        return $CURRENT_LANGUAGE;
    }

    $language = @$_GET['lang'] ?: @$_COOKIE['lang'] ?: @$_POST['lang'] ?: get_accept_language();
    
    if (!$language || !Language::get($language))
    {
        $language = Config::get('language');
    }

    $CURRENT_LANGUAGE = $language;
    return $language;
}

/**
* Given a message shortcode, returns an appropriately translated full-text string
*
* @param string $message_key The short message code
* @param string $language Optionally, the standard language code (defaults to the site default, then English)
* @return string Either the translated string, or the original English string, or an empty string
*/
function __($message_key, $language_code = "") {

    if (!$language_code)
    {
        $language_code = get_language();
    }
   
    return Language::get($language_code)->get_translation($message_key) 
        ?: Language::get('en')->get_translation($message_key) 
        ?: $message_key;
}

function change_viewer_language($newLanguage)
{
    set_cookie('lang', $newLanguage);
}
