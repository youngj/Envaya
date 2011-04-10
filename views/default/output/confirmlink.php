<?php

    /**
     * A link that displays a confirmation dialog before it executes a POST request
     *
     * @uses $vars['text'] The text of the link
     * @uses $vars['href'] The address
     * @uses $vars['confirm'] The dialog text
     *
     */

    $confirm = @$vars['confirm'] ?:__('areyousure');
    
    $link = view('output/post_url', array('href' => $vars['href']));

    if (@$vars['class']) {
        $class = 'class="' . $vars['class'] . '"';
    } else {
        $class = '';
    }
?>
<a href='<?php echo $link; ?>' <?php echo $class; ?> onclick='return confirm(<?php echo json_encode($confirm); ?>);'><?php echo escape($vars['text']); ?></a>