<?php
    $translate_url = $vars['translate_url'];
    if ($translate_url)
    {
        echo "<div style='padding:5px;text-align:center;font-weight:bold'>";
        echo "<a target='_blank' rel='nofollow' href='$translate_url'>".__('itrans:edit_page')."</a>";
        echo "</div>";
    }