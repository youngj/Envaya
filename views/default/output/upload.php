<?php
    $value = json_decode(@$vars['value'], true);

    if ($value && isset($value['original']))
    {
        $original = $value['original'];        
        echo "<a target='_blank' href='".escape($original['url'])."'>".escape($original['filename'])."</a>";
    }
?>