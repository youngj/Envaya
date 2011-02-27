<?php
    $value = json_decode(@$vars['value'], true);

    if ($value && isset($value[0]))
    {
        $original = $value[0];        
        echo "<a target='_blank' href='".escape($original['url'])."'>".escape($original['filename'])."</a>";
    }
?>