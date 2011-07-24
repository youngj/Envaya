<?php
    if (!isset($vars['show_translate_footer']) || $vars['show_translate_footer'])
    {
        $translate_url = PageContext::get_translation_url(true);
        if ($translate_url)
        {
            echo "<div style='padding:5px;text-align:center;'>";
            echo "<a target='_blank' rel='nofollow' href='$translate_url'>".__('itrans:edit_page')."</a>";
            echo "</div>";
        }
    }