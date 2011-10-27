<?php
    $site = $vars['site'];    
    $target = isset($vars['target']) ? "target='{$vars['target']}'" : '';
    echo "<div style='padding:2px'><a href='".escape($site->url)."' {$target}>".escape($site->title ?: $site->url)."</a></div>";
