<?php

class Language
{
    static $languages = array();
    
    static function get($code)
    {
        return @static::$languages[$code];
    }
    
    static function get_options()
    {        
        $options = array();
        foreach (static::$languages as $k => $v)
        {
            $options[$k] = __($k, $k);
        }

        return $options;
    }    
    
    static function init($code)
    {
        $lang = new Language($code);
        static::$languages[$code] = $lang;
        return $lang;    
    }
    
    protected $code;
    protected $translations;
  
    function __construct($code)
    {
        $this->code = $code;
        $this->translations = array();
        $this->loaded = false;
    }

    function add_translations($language_array)
    {
        $this->translations = $language_array + $this->translations;
    }
        
    function get_translation($key)
    {
        return @$this->translations[$key];
    }   
    
    private $loaded;
    
    function load()
    {
        if (!$this->loaded)
        {
            $path = dirname(dirname(__DIR__)) . "/languages";
            
            $this->add_translations(include("$path/{$this->code}.php"));

            if ($this->code == 'en')
            {
                $this->add_translations(include("$path/en_admin.php"));
            }
            $this->loaded = true;
        }
        return $this;
    }    
    
    function get_keys_by_prefix($prefix)
    {
         $keys = array();
         foreach ($this->translations as $k => $v)
         {
             if (strpos($k, $prefix) === 0)
             {
                 $keys[] = $k;
             }
         }
         return $keys;
    
    }
    
}


function init_languages()
{
    global $CONFIG;
    foreach ($CONFIG->languages as $code => $lang_name)
    {
        Language::init($code)->add_translations(
            array($code => $lang_name)
        );
    }
}
init_languages();

function get_current_language()
{
    return get_language() ?: 'en';
}

function get_cookie_language()
{
    global $CONFIG;

    if (isset($_COOKIE['lang']))
    {
        $lang = $_COOKIE['lang'];
        if (Language::get($lang))
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
function __($message_key, $language_code = "") {

    if (!$language_code)
    {
        $language_code = get_language();
    }
   
    return Language::get($language_code)->load()->get_translation($message_key) 
        ?: Language::get('en')->load()->get_translation($message_key) 
        ?: $message_key;
}

function get_language_link($lang)
{
    $name = escape(__($lang, $lang));

    if (get_language() == $lang)
    {
        return "<strong>$name</strong>";
    }
    else
    {
        $url = url_with_param(Request::instance()->full_original_url(), 'lang', $lang);

        return "<a href='".escape($url)."'>$name</a>";
    }
}

function get_language_links()
{
    $links = array();
    global $CONFIG;
    foreach (Language::$languages as $code => $v)
    {
        $links[] = get_language_link($code);
    }
    echo implode(' &middot; ', $links);
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
