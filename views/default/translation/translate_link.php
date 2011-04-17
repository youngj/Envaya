<?php
    $translations = $vars['translations'];
    
    $urlProps = array();
    foreach ($translations as $trans)
    {
        $urlProps[] = "prop[]={$trans->container_guid}.{$trans->property}.{$trans->html}";
    }
    $urlProps[] = "targetLang=".Language::get_current_code();

    $from = urlencode($_SERVER['REQUEST_URI']);
    $url = "/tr/translate?from=$from&".implode("&", $urlProps);

    echo "<a href='".escape($url)."'>".__("trans:contribute")."</a>";