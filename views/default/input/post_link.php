<?php
    /**
     * A link that executes a POST request     
     */

    $confirm = __('areyousure');
    $href = '';
    $html = null;       // HTML content of link
    $text = null;       // text content of link (set either 'html' or 'text')
    extract($vars);
         
    $attrs = Markup::get_attrs($vars, array(
        'class' => null,
        'style' => null,
        'id' => null,
    ));        
        
    if ($confirm)
    {
        $attrs['onclick'] = "return confirm(".json_encode($confirm).");";
    }
    
    if ($INCLUDE_COUNT == 0)
    {        
        echo "<script type='text/javascript'>".view('js/post_link')."</script>";
    }

    $ts = time();
    $token = generate_security_token($ts);
    
    $attrs['href'] = "javascript:postLink("
        .json_encode($href).", "
        .json_encode($ts).", "
        .json_encode($token).");";          
    
    if (!isset($html))
    {
        $html = escape($text);
    }
    
    echo "<a ".Markup::render_attrs($attrs).">{$html}</a>";
