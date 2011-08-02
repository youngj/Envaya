<?php

if (@$vars['show_translate_bar'] && PageContext::has_translation())
{    
    $origLang = PageContext::get_original_language();
    $viewLang = Language::get_current_code();   

    if ($origLang != $viewLang)
    {
        $transMode = TranslateMode::get_current();

        ob_start();    
            
        $origLangName = escape(__("lang:$origLang"));
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
                echo strtr(__("trans:stale_trans_from_to"), $tr);

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
                echo strtr(__("trans:trans_from_to"), $tr);

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
            if (PageContext::has_unsaved_translation())
            {
                echo strtr(__("trans:automatic_trans_error"), $tr);
            }
            else
            {
                echo strtr(__("trans:automatic_trans_from_to"), $tr);
            }

            $links[] = view('page_elements/translate_mode_link', array(
                'mode' => TranslateMode::None, 
                'text' => __("trans:view_original"),
                'original_url' => $vars['original_url'],
            ));        
        }
        else  // viewing original
        {
            echo sprintf(__("trans:page_original_in"), $origLangName);

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

        $translate_url = PageContext::get_translation_url(false);
        $links[] = "<a target='_blank' rel='nofollow' href='$translate_url'>".__('trans:edit')."</a>";
        
        echo " ".implode(' &middot; ', $links);
        
        $res = ob_get_clean();
        
        if ($res)
        {
            echo "<div id='translate_bar'>$res</div>";
        }
    }
}
?>