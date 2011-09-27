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
    
    static function split_text($text, $parts_per_message = 1, $boundary = ' ')
    {
        $chunks = array();
        
        $text = preg_replace('#( )+#', ' ', $text);
        
        // multipart sms can send 153 7-bit characters per message, single sms can send 160
        // (would be less for non-ascii characters, but we don't support them yet)
        
        $more = "\nMORE";        
        $more_len = strlen($more);
        $max_chunk = $parts_per_message * ($parts_per_message > 1 ? 153 : 160);               
        
        while (true)
        {
            $too_long = strlen($text) > $max_chunk;
            if (!$too_long)
            {
                $chunks[] = trim($text);
                break;
            }
            else
            {
                $chunk = Markup::truncate_at_boundary($text, $max_chunk - $more_len, $boundary);            
                $chunks[] = trim("$chunk$more");
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