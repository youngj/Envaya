<?php
    $class = $vars['class'];
    $href = $vars['href'];
    $text = $vars['text'];
    
    echo "<a class='icon_link $class' href='".escape($href)."'>".escape($text)."</a>";
