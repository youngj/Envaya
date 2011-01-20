<?php
    $value = json_decode(@$vars['value'], true);

    if ($value && isset($value['original']))
    {
        echo $value['original']['url'];
    }
?>