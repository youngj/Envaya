<?php

    $language = Language::current();
    
    $keys = implode(',', $language->get_requested_keys());
    $gz = gzcompress($keys, 4);
    $b64 = base64_encode($gz);
    
    $translate_url = "/tr/".Language::get_current_code() . "/page?keys=" .urlencode($b64);
    
    echo "<div style='padding:5px;text-align:center;font-weight:bold'>";
    echo "<a target='_blank' rel='nofollow' href='$translate_url'>".__('itrans:edit_page')."</a>";
    echo "</div>";
