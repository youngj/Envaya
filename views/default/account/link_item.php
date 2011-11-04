<?php
    $class = @$vars['class'];
    $style = @$vars['style'] ?: '';
    $href = $vars['href'];
    $text = $vars['text'];
    
    echo "<a class='icon_link $class' style='$style' href='".escape($href)."'>".escape($text)."</a>";
