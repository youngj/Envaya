<?php

    /**
     * Displays a large amount of text, with new lines converted to line breaks
     *
     * @uses $vars['text'] The text to display
     *
     */

    echo nl2br(Markup::parse_urls(escape($vars['value'])));
?>