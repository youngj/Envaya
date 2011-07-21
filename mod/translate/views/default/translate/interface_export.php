<?php

$lang = $vars['lang'];

$language = Language::get($lang);
$language->load_all();

$group_names = $language->get_loaded_files();

$newTrans = array();

foreach (Translation::filter_by_lang($lang) as $itrans)
{
    $newTrans[$itrans->key] = $itrans->value;
}

foreach ($group_names as $group_name)
{
    $modified = false;
    $keys = array_keys($language->get_group($group_name));

    foreach ($keys as $key)
    {
        if (isset($newTrans[$key]) && $newTrans[$key] != $language->get_translation($key))
        {
            $modified = true;
            break;
        }   
    }
    
    if ($modified)
    {
        echo "{$lang}_{$group_name}.php\n\n";

        echo "<?php \n";
        echo "return array(\n";
        foreach ($keys as $key)
        {
            $newValue = @$newTrans[$key] ?: $language->get_translation($key);
            if ($newValue)
            {
                echo "    ";
                var_export($key);
                echo " => ";
                var_export($newValue);
                echo ",\n";
            }
        }
        echo ");\n\n";
    }
}