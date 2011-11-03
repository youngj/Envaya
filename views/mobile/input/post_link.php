<?php
    $confirm = null;    // optional message of confirmation prompt
    $href = '';         // url of link
    $html = null;       // HTML content of link
    $text = null;       // text content of link (set either 'html' or 'text')
    extract($vars);
         
    $attrs = Markup::get_attrs($vars, array(
        'class' => null,
        'style' => null,
        'id' => null,
    ));        
        
    $ts = timestamp();
    $token = generate_security_token($ts);
    
    $attrs['href'] = "/pg/confirm_action?ok=".urlencode($href)
        ."&cancel=".urlencode($_SERVER['REQUEST_URI'])
        ."&message=".urlencode($confirm)
        ."&__token=$token&__ts=$ts";        
    
    if (!isset($html))
    {
        $html = escape($text);
    }
    
    echo "<a ".Markup::render_attrs($attrs).">{$html}</a>";         
    