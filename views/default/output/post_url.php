<?php
    $ts = time();
    $token = generate_security_token($ts);

    if ($vars['include_count'] == 0)
    {
        PageContext::add_header_html("<script type='text/javascript'>".view('js/post_link')."</script>");
    }
    
    echo "javascript:postLink(".json_encode($vars['href']).", ".json_encode($ts).", ".json_encode($token).");";  