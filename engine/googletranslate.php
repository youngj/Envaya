<?php

/*
 * Interface for automatically translating text and guessing the text's language.
 */
class GoogleTranslate
{
    static function is_supported_language($lang_code)
    {
        switch ($lang_code)
        {
            case 'en':
            case 'sw':
            case 'ar':
            case 'fr':
                return true;
            default:
                return false;
        }
    }
    
    static function get_auto_translation($text, $origLang, $viewLang)
    {
        if ($origLang == $viewLang
            || !static::is_supported_language($origLang)
            || !static::is_supported_language($viewLang)
            )
        {
            return null;
        }

        $text = trim($text);
        if (!$text)
        {
            return null;
        }

        $text = str_replace("\r","", $text);
        $text = str_replace("\n", ",;", $text);

        $url = "ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$origLang%7C$viewLang";
        
        // "To post a file, prepend a filename with @ and use the full path"
        // is a security vulnerability waiting to happen
        if ($text[0] == "@")
        {
            $text = " ".$text;
        } 
        
        $maxLength = 4999; // max limit for google translate api        
        $translatedChunks = array();
        
        $tooLong = strlen($text) > $maxLength;
        if ($tooLong)
        {
            $truncated = Markup::truncate_at_word_boundary($text, $maxLength);            
            $text = $truncated;
        }        
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, Config::get('domain'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('q' => $text));

        $json = curl_exec($ch);

        curl_close($ch);

        $res = json_decode($json);
        
        $translated = @$res->responseData->translatedText;
        if (!$translated)
        {
            return null;
        }

        $translated = html_entity_decode($translated, ENT_QUOTES);                    

        if ($tooLong)
        {
            $translated .= "...";
        }
            
        return str_replace(",;", "\n", $translated);
    }

    static function guess_language($text)
    {
        if (!$text)
        {
            return null;
        }

        $ch = curl_init();

        $url = "ajax.googleapis.com/ajax/services/language/detect?v=1.0&q=".urlencode(Markup::get_snippet($text, 500));
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, Config::get('domain'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $json = curl_exec($ch);

        curl_close($ch);

        $res = json_decode($json);

        $lang = $res->responseData->language;

        $languages = Config::get('languages');

        if (!$lang || !isset($languages[$lang]))
        {
            return null;
        }

        return $lang;
    }
}