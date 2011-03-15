<?php
    $ts = time();
    $token = generate_security_token($ts);

    if ($vars['include_count'] == 0)
    {
        PageContext::add_header_html('post_link', 
            "<script type='text/javascript'>".file_get_contents(Config::get('path')."_media/inline_js/post_link.js")."</script>"
        );
    }
    
    echo "javascript:postLink(".json_encode($vars['href']).", ".json_encode($ts).", ".json_encode($token).");";  