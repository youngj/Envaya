<?php
if (page_is_translatable())
{
?>
<div id='translate_bar'>
<?php
    $transMode = get_translate_mode();
    $origLang = get_original_language();
    $origLangName = escape(elgg_echo($origLang));
    $userLangName = escape(elgg_echo(get_language()));

    function trans_link($mode, $text)
    {
        $url = url_with_param($_SERVER['REQUEST_URI'],'trans',$mode);
        return "<a href='".escape($url)."'>$text</a>";
    }

    if ($transMode == TranslateMode::ManualOnly && !page_is_translatable(TranslateMode::ManualOnly))
    {
        $transMode = TranslateMode::None;
    }

    $links = array();

    if ($transMode == TranslateMode::ManualOnly) // viewing manual translation
    {
        if (page_has_stale_translation())
        {
            echo sprintf(elgg_echo("trans:stale_trans_from_to"), $origLangName, $userLangName);

            $links[] = trans_link(TranslateMode::All, elgg_echo("trans:view_stale_automatic"));
        }
        else if (page_is_translatable(TranslateMode::All))
        {
            echo sprintf(elgg_echo("trans:partial_trans_from_to"), $origLangName, $userLangName);

            $links[] = trans_link(TranslateMode::All, elgg_echo("trans:view_rest_automatic"));
        }
        else
        {
            echo sprintf(elgg_echo("trans:trans_from_to"), $origLangName, $userLangName);
        }

        $links[] = trans_link(TranslateMode::None, sprintf(elgg_echo("trans:view_original_in"), $origLangName));
    }
    else if ($transMode == TranslateMode::All) // viewing automatic translation
    {
        if (page_is_translatable(TranslateMode::ManualOnly))
        {
            echo sprintf(elgg_echo("trans:partial_automatic_trans_from_to"), $origLangName, $userLangName);
        }
        else
        {
            echo sprintf(elgg_echo("trans:automatic_trans_from_to"), $origLangName, $userLangName);
        }

        $links[] = trans_link(TranslateMode::None, sprintf(elgg_echo("trans:view_original_in"), $origLangName));
    }
    else  // viewing original
    {
        echo sprintf(elgg_echo("trans:page_original_in"), $origLangName);

        if (page_is_translatable(TranslateMode::ManualOnly))
        {
            $links[] = trans_link(TranslateMode::ManualOnly, sprintf(elgg_echo("trans:view_in"), $userLangName));
        }
        else if (page_is_translatable(TranslateMode::All))
        {
            $links[] = trans_link(TranslateMode::All, sprintf(elgg_echo("trans:view_automatic_in"), $userLangName));
        }
    }

    if (isadminloggedin())
    {
        $properties = page_translatable_properties();

        if (sizeof($properties))
        {
            $urlProps = array();
            foreach ($properties as $objProp)
            {
                $urlProps[] = "prop[]={$objProp[0]}.{$objProp[1]}";
            }

            $escUrl = urlencode($_SERVER['REQUEST_URI']);
            $links[] = "<a href='pg/org/translate?from=$escUrl&".implode("&", $urlProps)."'>".elgg_echo("trans:contribute")."</a>";
        }
    }

    echo " ".implode(' &middot; ', $links);
?>
<div style='clear:both'></div>
</div>
<?php
}
?>

