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
}