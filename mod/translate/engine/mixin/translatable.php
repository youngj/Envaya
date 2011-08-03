<?php

class Mixin_Translatable extends Mixin
{
    function translate_field($field, $lang = null)
    {        
        $text = trim($this->$field);
        if (!$text)
        {
            return '';
        }

        $origLang = $this->get_language();        
        
        if ($lang == null)
        {
            $lang = Language::get_current_code();        
        }

        $translateMode = TranslateMode::get_current();        
        
        if ($translateMode == TranslateMode::Disabled)
        {
            return $text;
        }
        
        $translation = $this->lookup_translation($field, $lang, $origLang, $translateMode);        
        
        if ($origLang != $lang)
        {
            PageContext::set_original_language($origLang);            
            PageContext::add_available_translation($translation);        
        }                

        if ($translateMode != TranslateMode::None)
        {
            return $translation->value;
        }
        else
        {
            return $text;
        }
    }

    private function lookup_translation($prop, $lang, $origLang, $translateMode)
    {
        $key = $this->get_translation_key($prop, $lang);
        if (!$key->guid)
        {
            $key->save();
        }
        
        $approvedTrans = $key->query_translations()
            ->where('approval > 0')
            ->order_by('approval_time desc')
            ->get();

        $doAutoTranslate = ($translateMode == TranslateMode::Automatic) 
            && ($origLang != $lang)
            && GoogleTranslate::is_supported_language($origLang)
            && GoogleTranslate::is_supported_language($lang);        
        
        if ($doAutoTranslate && (!$approvedTrans || $approvedTrans->is_stale()))
        {
            $autoTrans = $key->query_translations()
                ->where('owner_guid = 0')
                ->order_by('time_created desc')
                ->get();
        
            if ($autoTrans && !$autoTrans->is_stale())
            {
                return $autoTrans;
            }
            else
            {            
                $key->queue_auto_translation();
            
                $tempTrans = $key->new_translation();
                $tempTrans->value = __('trans:translating', $lang);
                return $tempTrans;
            }
        }
        
        if ($approvedTrans)
        {
            return $approvedTrans;            
        }
        else
        {        
            // return translation with untranslated text
            $tempTrans = $key->new_translation();
            $tempTrans->value = $this->$prop;
            return $tempTrans;
        }
    }    
    
    function get_translation_key($prop, $lang)
    {
        $guid = $this->guid;
        $key_name = "entity:{$guid}:{$prop}";
        
        $language = TranslationLanguage::get_by_code($lang);
        
        $key = $language->query_keys()->where('name = ?', $key_name)->get();        
        if (!$key)
        {        
            $key = new EntityTranslationKey();
            $key->name = $key_name;
            $key->container_guid = $guid;
            $key->language_guid = $language->guid;
        }
        return $key;
    }    
}