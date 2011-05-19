<?php

    /**
     * A link that executes a POST request
     *
     * @uses $vars['text'] The text of the link
     * @uses $vars['href'] The address
     * @uses $vars['confirm'] The dialog text
     *
     */

    $confirm = @$vars['confirm'] ?:__('areyousure');
    
    if (isset($vars['class'])) 
    {
        $class = 'class="' . $vars['class'] . '"';
    } 
    else 
    {
        $class = '';
    }
    
    $js = isset($vars['js']) ? $vars['js'] : '';
    
    $onclick = '';
    if (@$vars['confirm'])
    {
        $onclick = "onclick='return confirm(".json_encode($confirm).");'";
    }
    
    if ($INCLUDE_COUNT == 0)
    {        
        echo "<script type='text/javascript'>".view('js/post_link')."</script>";
    }

    $ts = time();
    $token = generate_security_token($ts);
    $link = "javascript:postLink(".json_encode($vars['href']).", ".json_encode($ts).", ".json_encode($token).");";      
    
    if (isset($vars['html']))
    {
        $html = $vars['html'];
    }
    else
    {
        $html = escape($vars['text']);
    }
    
    echo "<a href='{$link}' {$js} {$class} {$onclick}>{$html}</a>";
