<?php

    /**
     * Displays a URL as a link
     *
     * @uses $vars['value'] The URL to display
     *
     */

    $val = trim($vars['value']);
    if (!empty($val)) {
        if ((substr_count($val, "http://") == 0) && (substr_count($val, "https://") == 0)) {
            $val = "http://" . $val;
        }

        if ($vars['is_action'])
        {
            $ts = time();
            $token = generate_security_token($ts);

            $sep = "?";
            if (strpos($val, '?')>0) $sep = "&";
            $val = "$val{$sep}__token=$token&__ts=$ts";
        }

        $val = escape($val);

        $text = @$vars['text'] ?: $val;

        echo "<a href=\"{$val}\" target=\"_blank\">$text</a>";
    }

?>