<?php

/**
* Elgg language module
* Functions to manage language and translations.
*
* @package Elgg
* @subpackage Core

* @author Curverider Ltd

* @link http://elgg.org/
*/

/**
* Add a translation.
*
* Translations are arrays in the Zend Translation array format, eg:
*
*   $english = array('message1' => 'message1', 'message2' => 'message2');
*  $german = array('message1' => 'Nachricht1','message2' => 'Nachricht2');
*
* @param string $country_code Standard country code (eg 'en', 'nl', 'es')
* @param array $language_array Formatted array of strings
* @return true|false Depending on success
*/

function add_translation($country_code, $language_array)
{
    global $CONFIG;
    if (!isset($CONFIG->translations))
        $CONFIG->translations = array();

    if (!isset($CONFIG->translations[$country_code]))
    {
        $CONFIG->translations[$country_code] = $language_array;
    }
    else
    {
        $CONFIG->translations[$country_code] = $language_array + $CONFIG->translations[$country_code];
    }
}

/**
* Detect the current language being used by the current site or logged in user.
*
*/
function get_current_language()
{
    global $CONFIG;

    $language = get_language();

    if (!$language)
        $language = 'en';

    return $language;
}

function get_cookie_language()
{
    global $CONFIG;

    if (isset($_COOKIE['lang']))
    {
        $lang = $_COOKIE['lang'];
        if (isset($CONFIG->translations[$lang]))
        {
            return $lang;
        }
    }
}

function get_accept_language()
{
    global $CONFIG;

    $acceptLanguage = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if ($acceptLanguage)
    {
        $languages = explode(",", $acceptLanguage);
        foreach ($languages as $language)
        {
            $langQ = explode(";", $language);
            $lang = trim($langQ[0]);
            $langLocale = explode("-", $lang);
            $lang = $langLocale[0];

            if (isset($CONFIG->translations[$lang]))
            {
                return $lang;
            }
        }
    }
}

/**
* Gets the current language in use by the system or user.
* @return string The language code (eg "en")
*/
function get_language()
{
    global $CONFIG;

    $language = '';

    global $CURRENT_LANGUAGE;
    if ($CURRENT_LANGUAGE)
    {
        return $CURRENT_LANGUAGE;
    }

    if (!$language)
    {
        $language = @$_GET['lang'];
    }

    if (!$language)
    {
        $language = get_cookie_language();
    }

    if (!$language)
    {
        $language = get_accept_language();
    }

    if ((!$language) && (isset($CONFIG->language)) && ($CONFIG->language))
    {
        $language = $CONFIG->language;
    }

    if ($language)
    {
        $CURRENT_LANGUAGE = $language;
        return $language;
    }
    return false;

}

/**
* Given a message shortcode, returns an appropriately translated full-text string
*
* @param string $message_key The short message code
* @param string $language Optionally, the standard language code (defaults to the site default, then English)
* @return string Either the translated string, or the original English string, or an empty string
*/
function elgg_echo($message_key, $language = "") {

    global $CONFIG;

    if (!$language)
    {
        $language = get_language();
    }
    else
    {
        load_translation($language);
    }

    if (isset($CONFIG->translations[$language][$message_key])) {
        return $CONFIG->translations[$language][$message_key];
    } else if (isset($CONFIG->translations["en"][$message_key])) {
        return $CONFIG->translations["en"][$message_key];
    }

    return $message_key;

}

function load_all_translations()
{
    $path = dirname(dirname(__DIR__)) . "/languages/";

    if ($handle = opendir($path))
    {
        while ($language = readdir($handle))
        {
            if (endswith($language, '.php'))
            {
                include_once($path . $language);
            }
        }
    }
    else
        error_log("Missing translation path $path");

}

function load_translation($lang)
{
    $path = dirname(dirname(__DIR__)) . "/languages/";

    include_once("$path$lang.php");

    if ($lang != 'en')
    {
        include_once("{$path}en.php");
    }
}

/**
* Return an array of installed translations as an associative array "two letter code" => "native language name".
*/
function get_installed_translations($show_completeness = false)
{
    global $CONFIG;

    $installed = array();

    foreach ($CONFIG->translations as $k => $v)
    {
        $installed[$k] = elgg_echo($k, $k);

        if ($show_completeness)
        {
            $completeness = get_language_completeness($k);
            if ((isadminloggedin()) && ($completeness<100) && ($k!='en'))
                $installed[$k] .= " (" . $completeness . "% " . elgg_echo('complete') . ")";
        }
    }

    return $installed;
}

/**
* Return the level of completeness for a given language code (compared to english)
*/
function get_language_completeness($language)
{
    global $CONFIG;

    load_translation($language);

    $en = count($CONFIG->translations['en']) - count($CONFIG->en_admin);

    $missing = count(get_missing_language_keys($language));

    $lang = $en - $missing;

    return round(($lang / $en) * 100, 2);
}

/**
* Return the translation keys missing from a given language
*/
function get_missing_language_keys($language)
{
    global $CONFIG;

    load_translation($language);

    $missing = array();

    foreach ($CONFIG->translations['en'] as $k => $v)
    {
        if (!isset($CONFIG->translations[$language][$k]) && !isset($CONFIG->en_admin[$k]))
            $missing[] = $k;
    }

    return $missing;
}

function get_language_link($lang)
{
    $name = escape(elgg_echo($lang, $lang));

    if (get_language() == $lang)
    {
        return "<strong>$name</strong>";
    }
    else
    {
        $url = url_with_param($_SERVER['REQUEST_URI'], 'lang', $lang);

        return "<a href='".escape($url)."'>$name</a>";
    }
}

function get_language_links()
{
    $links = array();
    global $CONFIG;
    foreach ($CONFIG->translations as $lang => $v)
    {
        $links[] = get_language_link($lang);
    }
    echo implode(' &middot; ', $links);
}

function get_translatable_language_keys()
{
    global $CONFIG;
    $keys = array();

    foreach ($CONFIG->translations['en'] as $k => $v)
    {
        if (!isset($CONFIG->en_admin[$k]))
            $keys[] = $k;
    }

    return $keys;
}

function get_language_keys_by_prefix($prefix)
{
    $keys = array();
    global $CONFIG;
    foreach ($CONFIG->translations['en'] as $k => $v)
    {
        if (strpos($k, $prefix) === 0)
        {
            $keys[] = $k;
        }
    }
    return $keys;
}


function change_viewer_language($newLanguage)
{
    global $CONFIG;

    $expireTime = time() + 60 * 60 * 24 * 365 * 15;

    if ($CONFIG->cookie_domain)
    {
        setcookie("lang", $newLanguage, $expireTime, '/', $CONFIG->cookie_domain);
    }
    setcookie("lang", $newLanguage, $expireTime, '/');
}

restore_query_params();
if (@$_GET['lang'])
{
    change_viewer_language($_GET['lang']);
}
load_translation(get_current_language());
