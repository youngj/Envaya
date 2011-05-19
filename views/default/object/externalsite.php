<?php
    $entity = $vars['entity'];
    
    $target = isset($vars['target']) ? "target='{$vars['target']}'" : '';

    echo "<div style='padding:2px'><a href='".escape($entity->url)."' {$target}>".escape($entity->title ?: $entity->url)."</a></div>";
