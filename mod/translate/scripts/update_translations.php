<?php 

$root = dirname(dirname(dirname(__DIR__)));

require_once "$root/start.php";

foreach (Config::get('languages') as $code)
{
    echo "$code\n";
    $language = TranslationLanguage::get_by_code($code);
    if ($language && $language->guid)
    {
        foreach ($language->query_groups()->where('status = ?', InterfaceGroup::Enabled)->filter() as $group)
        {
            $group->update_defined_translations();
        }
    }
}
