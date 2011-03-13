<?php

    /**
     * A link that displays a confirmation dialog before it executes
     *
     * @uses $vars['text'] The text of the link
     * @uses $vars['href'] The address
     * @uses $vars['confirm'] The dialog text
     *
     */

    $confirm = @$vars['confirm'] ?:__('areyousure');

    $link = $vars['href'];

    if ($vars['is_action'])
    {
        $ts = time();
        $token = generate_security_token($ts);

        if ($vars['include_count'] == 0)
        {
            PageContext::add_header_html('post_link', 
                "<script type='text/javascript'>".file_get_contents(Config::get('path')."_media/inline_js/post_link.js")."</script>"
            );
        }
        
        $link = "javascript:postLink(".json_encode($link).", ".json_encode($ts).", ".json_encode($token).");";        
    }

    if (@$vars['class']) {
        $class = 'class="' . $vars['class'] . '"';
    } else {
        $class = '';
    }
?>
<a href='<?php echo $link; ?>' <?php echo $class; ?> onclick='return confirm(<?php echo json_encode($confirm); ?>);'><?php echo escape($vars['text']); ?></a>