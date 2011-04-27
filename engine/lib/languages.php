<?php

function init_languages()
{
    foreach (Config::get('languages') as $code => $lang_name)
    {
        Language::init($code)->add_translations(
            array("lang:$code" => $lang_name)
        );
    }
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
        $language_code = Language::get_current_code();
    }
   
    return Language::get($language_code)->get_translation($message_key) 
        ?: Language::get('en')->get_translation($message_key) 
        ?: $message_key;
}

function change_viewer_language($newLanguage)
{
    set_cookie('lang', $newLanguage);
}
