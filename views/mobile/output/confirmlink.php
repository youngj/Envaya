<?php

    $confirm = @$vars['confirm'] ?: __('areyousure');
    
    $ts = time();
    $token = generate_security_token($ts);

    $url = "/pg/confirm_action?ok=".urlencode($vars['href'])
        ."&cancel=".urlencode($_SERVER['REQUEST_URI'])
        ."&message=".urlencode($confirm)
        ."&__token=$token&__ts=$ts";

?>
<a href='<?php echo escape($url); ?>' class='<?php echo @$vars['class']; ?>'><?php echo escape($vars['text']); ?></a>