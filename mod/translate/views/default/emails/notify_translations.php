<?php

$translations = $vars['translations'];

foreach ($translations as $translation)
{
    $key = $translation->get_container_entity();
    echo abs_url("{$key->get_url()}?translation={$translation->guid}");
    echo "\n\n";
}

echo "\n";