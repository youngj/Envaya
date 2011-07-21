<?php
if (@$vars['show_translate_bar'] && PageContext::has_translation())
{
?>
<div id='translate_bar'>
<?php
    $transMode = TranslateMode::get_current();
    $origLang = PageContext::get_original_language();
    $viewLang = Language::get_current_code();
    $origLangName = escape(__("lang:$origLang"));
    $userLangName = escape(__("lang:$viewLang"));
    
    $can_auto_translate = GoogleTranslate::is_supported_language($origLang) 
        && GoogleTranslate::is_supported_language($viewLang);

    if ($transMode == TranslateMode::ManualOnly && !PageContext::has_translation(TranslateMode::ManualOnly))
    {
        $transMode = TranslateMode::None;
    }

    $links = array();
    
    $tr = array('{origlang}' => $origLangName, '{curlang}' => $userLangName);

    if ($transMode == TranslateMode::ManualOnly) // viewing manual translation
    {
        if (PageContext::has_stale_translation())
        {
            echo strtr(__("trans:stale_trans_from_to"), $tr);

            if ($can_auto_translate)
            {
                $links[] = view('translation/mode_link', array(
                    'mode' => TranslateMode::All, 
                    'text' => __("trans:view_stale_automatic"),
                    'original_url' => $vars['original_url'],
                ));
            }
        }
        else if (PageContext::has_translation(TranslateMode::All))
        {
            echo strtr(__("trans:partial_trans_from_to"), $tr);

            if ($can_auto_translate)
            {
                $links[] = view('translation/mode_link', array(
                    'mode' => TranslateMode::All, 
                    'text' => __("trans:view_rest_automatic"),
                    'original_url' => $vars['original_url'],
                ));         
            }
        }
        else
        {
            echo strtr(__("trans:trans_from_to"), $tr);
        }

        $links[] = view('translation/mode_link', array(
            'mode' => TranslateMode::None, 
            'text' => sprintf(__("trans:view_original_in"), $origLangName),
            'original_url' => $vars['original_url'],
        ));
    }
    else if ($transMode == TranslateMode::All) // viewing automatic translation
    {
        if (PageContext::has_translation_error())
        {
            echo strtr(__("trans:automatic_trans_error"), $tr);
        }
        else if (PageContext::has_translation(TranslateMode::ManualOnly))
        {
            echo strtr(__("trans:partial_automatic_trans_from_to"), $tr);
        }
        else
        {
            echo strtr(__("trans:automatic_trans_from_to"), $tr);
        }

        $links[] = view('translation/mode_link', array(
            'mode' => TranslateMode::None, 
            'text' => sprintf(__("trans:view_original_in"), $origLangName),
            'original_url' => $vars['original_url'],
        ));        
    }
    else  // viewing original
    {
        echo sprintf(__("trans:page_original_in"), $origLangName);

        if (PageContext::has_translation(TranslateMode::ManualOnly))
        {           
            $links[] = view('translation/mode_link', array(
                'mode' => TranslateMode::ManualOnly, 
                'text' => sprintf(__("trans:view_in"), $userLangName),
                'original_url' => $vars['original_url'],
            ));                    
        }
        else if (PageContext::has_translation(TranslateMode::All))
        {
            if ($can_auto_translate)
            {        
                $links[] = view('translation/mode_link', array(
                    'mode' => TranslateMode::All, 
                    'text' => sprintf(__("trans:view_automatic_in"), $userLangName),
                    'original_url' => $vars['original_url'],
                ));                    
            }
        }
    }

    echo " ".implode(' &middot; ', $links);
?>
<div style='clear:both'></div>
</div>
<?php
}
?>