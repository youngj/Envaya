<?php
    $value = json_decode(@$vars['value'], true);

    if ($value && isset($value[0]))
    {
        echo $value[0]['url'];
    }
?>