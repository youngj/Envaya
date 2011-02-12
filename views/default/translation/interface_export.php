<?php

$lang = $vars['lang'];

$keys = get_translatable_language_keys();

$newTrans = array();

foreach (InterfaceTranslation::filterByLang($lang) as $itrans)
{
    $newTrans[$itrans->key] = $itrans->value;
}

echo "array(\n";
foreach ($keys as $key)
{
    $newValue = @$newTrans[$key] ?: Language::get($lang)->get_translation($key);
    if ($newValue)
    {
        echo "\t";
        var_export($key);
        echo " => ";
        var_export($newValue);
        echo ",\n";
    }
}
echo ");";