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
        $link = view('output/post_url', array('href' => $link));
    }

    if (@$vars['class']) {
        $class = 'class="' . $vars['class'] . '"';
    } else {
        $class = '';
    }
?>
<a href='<?php echo $link; ?>' <?php echo $class; ?> onclick='return confirm(<?php echo json_encode($confirm); ?>);'><?php echo escape($vars['text']); ?></a>