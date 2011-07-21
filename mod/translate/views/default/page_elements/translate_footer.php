<?php

    $lang = Language::get_current_code();

    $keys = array();
    foreach (PageContext::get_available_translations() as $trans)
    {
         $keys[] = $trans->get_container_entity()->name;
    }

    if ($lang != Config::get('language'))
    {    
        $keys = array_merge($keys, Language::current()->get_requested_keys());            
    }
    
    if ($keys)
    {
        $uri_snippet = Request::get_uri();
        $max_uri = 50;
        if (strlen($uri_snippet) > $max_uri)
        {
            $uri_snippet = substr($uri_snippet, 0, $max_uri). "...";
        }
    
        // compress keys in URL to try to stay under the 2083 byte maximum length for internet explorer under most circumstances        
        $b64 = base64_encode(gzcompress(
            $uri_snippet . ' ' .
            implode(',', $keys), 4));
            
        $b64 = rtrim($b64, '='); // trailing = signs not necessary for php base64_decode
        
        $translate_url = "/tr/$lang/page/".urlencode_alpha($b64);
        
        echo "<div style='padding:5px;text-align:center;font-weight:bold'>";
        echo "<a target='_blank' rel='nofollow' href='$translate_url'>".__('itrans:edit_page')."</a>";
        echo "</div>";
    }
