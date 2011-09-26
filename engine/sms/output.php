<?php

class SMS_Output
{    
    static function text_from_html($value)
    {
        $value = preg_replace('/<img [^>]+>/', __('sms:image_placeholder'), $value);
        $value = preg_replace('/<scribd [^>]+>/', __('sms:document_placeholder'), $value);    
                
        return Markup::snippetize_html($value, 10000, array(
            'HTML.AllowedElements' => '',
            'AutoFormat.Linkify' => false,
            'AutoFormat.RemoveEmpty' => true
        ));        
    }
    
    static function split_text($text, $maxLength)
    {
        $chunks = array();
        
        while (true)
        {
            $tooLong = strlen($text) > $maxLength;
            if (!$tooLong)
            {
                $chunks[] = $text;
                break;
            }
            else
            {
                $chunk = Markup::truncate_at_word_boundary($text, $maxLength);            
                $chunks[] = "$chunk [MORE]";
                $text = substr($text, strlen($chunk));            
            }
        }
        return $chunks;
    }

    static function short_time($time)
    {
        $dateTime = new DateTime("@{$time}");
        $now = new DateTime("@".timestamp());
        
        $year = $dateTime->format('Y');
            
        if ($now->format('Y') != $year)
        {
            return $dateTime->format('jMY');
        }
        else
        {
            return $dateTime->format('jM');
        }
        
        
    }
}