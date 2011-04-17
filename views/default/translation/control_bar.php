<?php
if (PageContext::is_translatable())
{
?>
<div id='translate_bar'>
<?php
    $transMode = TranslateMode::get_current();
    $origLang = PageContext::get_original_language();
    $origLangName = escape(__($origLang));
    $userLangName = escape(__(Language::get_current_code()));

    function trans_link($mode, $text)
    {
        $url = url_with_param(Request::full_original_url(),'trans',$mode);
        return "<a href='".escape($url)."'>$text</a>";
    }

    if ($transMode == TranslateMode::ManualOnly && !PageContext::is_translatable(TranslateMode::ManualOnly))
    {
        $transMode = TranslateMode::None;
    }

    $links = array();

    if ($transMode == TranslateMode::ManualOnly) // viewing manual translation
    {
        if (PageContext::has_stale_translation())
        {
            echo sprintf(__("trans:stale_trans_from_to"), $origLangName, $userLangName);

            $links[] = trans_link(TranslateMode::All, __("trans:view_stale_automatic"));
        }
        else if (PageContext::is_translatable(TranslateMode::All))
        {
            echo sprintf(__("trans:partial_trans_from_to"), $origLangName, $userLangName);

            $links[] = trans_link(TranslateMode::All, __("trans:view_rest_automatic"));
        }
        else
        {
            echo sprintf(__("trans:trans_from_to"), $origLangName, $userLangName);
        }

        $links[] = trans_link(TranslateMode::None, sprintf(__("trans:view_original_in"), $origLangName));
    }
    else if ($transMode == TranslateMode::All) // viewing automatic translation
    {
        if (PageContext::has_translation_error())
        {
            echo sprintf(__("trans:automatic_trans_error"), $origLangName, $userLangName);
        }
        else if (PageContext::is_translatable(TranslateMode::ManualOnly))
        {
            echo sprintf(__("trans:partial_automatic_trans_from_to"), $origLangName, $userLangName);
        }
        else
        {
            echo sprintf(__("trans:automatic_trans_from_to"), $origLangName, $userLangName);
        }

        $links[] = trans_link(TranslateMode::None, sprintf(__("trans:view_original_in"), $origLangName));
    }
    else  // viewing original
    {
        echo sprintf(__("trans:page_original_in"), $origLangName);

        if (PageContext::is_translatable(TranslateMode::ManualOnly))
        {
            $links[] = trans_link(TranslateMode::ManualOnly, sprintf(__("trans:view_in"), $userLangName));
        }
        else if (PageContext::is_translatable(TranslateMode::All))
        {
            $links[] = trans_link(TranslateMode::All, sprintf(__("trans:view_automatic_in"), $userLangName));
        }
    }

    if (Session::isadminloggedin())
    {
        $translations = PageContext::get_available_translations();
        
        if (sizeof($translations))
        {
            $links[] = view('translation/translate_link', array('translations' => $translations));
        }
    }

    echo " ".implode(' &middot; ', $links);
?>
<div style='clear:both'></div>
</div>
<?php
}
?>

