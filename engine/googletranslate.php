<?php

/*
 * Interface for automatically translating text and guessing the text's language.
 */
class GoogleTranslate
{
    static function get_auto_translation($text, $origLang, $viewLang)
    {
        if ($origLang == $viewLang)
        {
            return null;
        }

        $text = trim($text);
        if (!$text)
        {
            return null;
        }

        $ch = curl_init();

        $text = str_replace("\r","", $text);
        $text = str_replace("\n", ",;", $text);

        $url = "ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$origLang%7C$viewLang";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, "www.envaya.org");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        
        // "To post a file, prepend a filename with @ and use the full path"
        // is a security vulnerability waiting to happen
        if ($text[0] == "@")
        {
            $text = " ".$text;
        } 
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('q' => $text));

        $json = curl_exec($ch);

        curl_close($ch);

        $res = json_decode($json);

        $translated = @$res->responseData->translatedText;
        if (!$translated)
        {
            return null;
        }

        $text = html_entity_decode($translated, ENT_QUOTES);

        return str_replace(",;", "\n", $text);
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
        curl_setopt($ch, CURLOPT_REFERER, "www.envaya.org");
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