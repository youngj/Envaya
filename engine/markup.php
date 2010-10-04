<?php

class Markup
{
    /**
     * Takes a string and turns any URLs into formatted links
     *
     * @param string $text The input string
     * @return string The output stirng with formatted links
     **/
    static function parse_urls($text) {

        return preg_replace_callback('/(?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i',
        create_function(
            '$matches',
            '
                $url = $matches[1];
                $urltext = str_replace("/", "/<wbr />", $url);
                return "<a href=\"$url\" style=\"text-decoration:underline;\">$urltext</a>";
            '
        ), $text);
    }

    static function autop($p, $br = 1)
    {
        $p = str_replace(array("\r\n", "\r"), "\n", $p); // cross-platform newlines
        $p = preg_replace("/\n\n+/", "\n\n", $p); // take care of duplicates
        $p = trim($p);
        $p = preg_replace("/\n/", "<br />", $p);
        return $p;
    }

    static function get_snippet($content, $maxLength = 100)
    {
        if ($content)
        {
            $cacheKey = "snippet_".md5($content)."_$maxLength";
            $cache = get_cache();
            $snippet = $cache->get($cacheKey);
            if (!$snippet)
            {
                $content = preg_replace('/<img[^>]+>/i', '', $content);
                $content = preg_replace('/<\/(p|h1|h2|h3)>/i', '</$1> <br />', $content);

                $tooLong = strlen($content) > $maxLength;
                // todo: multi-byte support
                if ($tooLong)
                {
                    $shortStr = substr($content, 0, $maxLength);

                    $lastSpace = strrpos($shortStr, ' ');
                    if ($lastSpace && $lastSpace > $maxLength / 2)
                    {
                        $shortStr = substr($shortStr, 0, $lastSpace);
                    }

                    $content = $shortStr;
                }
                $content = Markup::sanitize_html($content, array('HTML.AllowedElements' => 'a,em,strong,br','AutoFormat.RemoveEmpty' => true));
                $content = mb_ereg_replace('(\xc2\xa0)+',' ',$content); # non-breaking space
                $content = preg_replace('/(<br \/>\s*)+/', ' &ndash; ', $content);
                $content = preg_replace('/&ndash;\s*$/', '', $content);
                $content = preg_replace('/^\s*&ndash;/', '', $content);
                $content = preg_replace('/(&nbsp;)+/', ' ', $content);

                if ($tooLong)
                {
                    $content = $content."...";
                }
                $snippet = $content;
                $cache->set($cacheKey, $snippet);                               
            }

            return $snippet;
        }
        return '';
    }
    
    static function sanitize_html($html, $options = null)
    {
        require_once(dirname(__DIR__).'/vendors/htmlpurifier/library/HTMLPurifier.auto.php');
        global $CONFIG;

        if (!$options)
        {
            $options = array();
        }
        $options['Cache.SerializerPath'] = $CONFIG->dataroot;
        $options['AutoFormat.Linkify'] = true;        
        
        $purifier = new HTMLPurifier($options);
        return $purifier->purify( $html );
    }

}