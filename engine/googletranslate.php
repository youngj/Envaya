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
            /* case 'en':
            case 'sw':
            case 'ar':
            case 'fr':
                return true; */
            default:
                return false;
        }
    }
    
    static function get_auto_translation($text, $origLang, $viewLang, $is_html = true)
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

        if ($is_html)
        {
            $deflater = new Markup_HtmlDeflater();
            
            if (strlen($text) > 50000)
            {
                $text = substr($text, 0, 50000);
            }
            
            $text = $deflater->deflate($text);
            $format = 'html';
        }
        else        
        {
            $text = preg_replace('#\s+#', ' ', $text);
            $format = 'text';
        }
        
        $key = Config::get('google:api_key');        
        $url = "https://www.googleapis.com/language/translate/v2?key=$key&source=$origLang&target=$viewLang&format=$format";
        
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
        
        //error_log($text);
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, Config::get('domain'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('q' => $text));

        $json = curl_exec($ch);

        curl_close($ch);

        $res = json_decode($json, true);
        
        $translated = @$res['data']['translations'][0]['translatedText'];
        if (!$translated)
        {
            throw new GoogleTranslateException($json);
        }

        $translated = html_entity_decode($translated, ENT_QUOTES);          
        if ($tooLong)
        {
            $translated .= "...";
        }
        
        if ($is_html)
        {
            $translated = $deflater->inflate($translated);
        }
        
        return $translated;
    }

    static function guess_language($text, $is_html = true)
    {
        if (!$text)
        {
            return null;
        }

        $snippet = Markup::get_snippet($text, 200);
        if (!$snippet)
        {
            return null;
        }
        
        $ch = curl_init();
        
        $key = Config::get('google:api_key');
        $url = "https://www.googleapis.com/language/translate/v2/detect?key=$key&q=".urlencode($snippet);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_REFERER, Config::get('domain'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $json = curl_exec($ch);

        curl_close($ch);

        $res = json_decode($json, true);
        
        $detection = @$res['data']['detections'][0][0];
        if (!$detection)
        {
            throw new GoogleTranslateException($json);
        }
        
        $confidence = (float)$detection['confidence'];        
        $lang = $detection['language'];
        
        //error_log("Google Translate guessed '$lang' with $confidence confidence");
        
        if ($confidence < 0.03)
        {
            throw new GoogleTranslateNoConfidenceException($confidence);
        }
        
        $languages = Config::get('languages');
        if (!$lang || !in_array($lang, $languages))
        {
            throw new GoogleTranslateUnsupportedLanguageException($lang);
        }
        
        return $lang;
    }
}

class GoogleTranslateException extends Exception {}
class GoogleTranslateNoConfidenceException extends GoogleTranslateException {}
class GoogleTranslateUnsupportedLanguageException extends GoogleTranslateException {}