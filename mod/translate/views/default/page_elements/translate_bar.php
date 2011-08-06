<?php

if (!@$vars['hide_translate_bar'] && PageContext::has_translation())
{    
    $origLang = PageContext::get_original_language();
    $viewLang = Language::get_current_code();   

    if ($origLang != $viewLang)
    {
        $transMode = TranslateMode::get_current();
            
        $origLangName = escape($origLang ? __("lang:$origLang") : __('lang:unknown'));
        $userLangName = escape(__("lang:$viewLang"));
        
        $can_auto_translate = PageContext::has_translation(TranslateMode::Automatic)
            && GoogleTranslate::is_supported_language($origLang) 
            && GoogleTranslate::is_supported_language($viewLang);
            
        if ($transMode == TranslateMode::Approved && !PageContext::has_translation(TranslateMode::Approved))
        {
            $transMode = TranslateMode::None;
        }

        $links = array();
        
        $tr = array('{origlang}' => $origLangName, '{curlang}' => $userLangName);

        if ($transMode == TranslateMode::Approved) 
        {
            if (PageContext::has_stale_translation())
            {
                $msg = strtr(__("trans:stale_trans_from_to"), $tr);

                if ($can_auto_translate)
                {
                    $links[] = view('page_elements/translate_mode_link', array(
                        'mode' => TranslateMode::Automatic, 
                        'text' => __("trans:view_automatic"),
                        'original_url' => $vars['original_url'],
                    ));
                }
            }
            else 
            {
                $msg = strtr(__("trans:trans_from_to"), $tr);

                if ($can_auto_translate)
                {
                    $links[] = view('page_elements/translate_mode_link', array(
                        'mode' => TranslateMode::Automatic, 
                        'text' => __("trans:view_rest_automatic"),
                        'original_url' => $vars['original_url'],
                    ));         
                }
            }             

            $links[] = view('page_elements/translate_mode_link', array(
                'mode' => TranslateMode::None, 
                'text' => __("trans:view_original"),
                'original_url' => $vars['original_url'],
            ));                        
        }
        else if ($transMode == TranslateMode::Automatic) // viewing automatic translation
        {               
            $unsaved_translations = array_filter(PageContext::get_available_translations(), 
                function($t) { 
                    return $t->source == Translation::GoogleTranslate && !$t->guid;                 
                });
                    
            if ($unsaved_translations)
            {
                $msg = view('page_elements/wait_for_translations', array(
                    'unsaved_translations' => $unsaved_translations,
                    'waiting_message' => strtr(__("trans:waiting"), $tr),
                    'error_message' => strtr(__('trans:automatic_trans_error'), $tr)
                ));
            }
            else
            {
                $msg = strtr(__("trans:automatic_trans_from_to"), $tr);
            }

            $links[] = view('page_elements/translate_mode_link', array(
                'mode' => TranslateMode::None, 
                'text' => __("trans:view_original"),
                'original_url' => $vars['original_url'],
            ));        
        }
        else if ($origLang) // viewing original in a known language
        {
            $msg = sprintf(__("trans:page_original_in"), $origLangName);

            if (PageContext::has_translation(TranslateMode::Approved))
            {           
                $links[] = view('page_elements/translate_mode_link', array(
                    'mode' => TranslateMode::Approved, 
                    'text' => sprintf(__("trans:view_in"), $userLangName),
                    'original_url' => $vars['original_url'],
                ));                    
            }
            else if ($can_auto_translate)
            {        
                $links[] = view('page_elements/translate_mode_link', array(
                    'mode' => TranslateMode::Automatic, 
                    'text' => __("trans:view_automatic"),
                    'original_url' => $vars['original_url'],
                ));
            }
        }
        else // viewing original in an unknown language
        {
            return;
        }

        $translate_url = PageContext::get_translation_url(false);
        $links[] = "<a target='_blank' rel='nofollow' href='$translate_url'>".__('trans:edit')."</a>";
        
        echo "<div id='translate_bar'>";
        echo $msg." ".implode(' &middot; ', $links);
        echo "</div>";
    }
}
?>