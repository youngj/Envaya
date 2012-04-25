<?php

/*
 * Extension for the Entity class that allows entity properties to be translated.
 */
class Handler_TranslateEntityField
{
    static function execute($vars)
    {        
        $text = trim($vars['value']);
        if (!$text)
        {
            return $vars;
        }
        
        $entity = $vars['entity'];
        $origLang = $entity->language; // may be empty
        $lang = Language::get_current_code();
        
        $translateMode = TranslateMode::get_current();
        if ($translateMode == TranslateMode::Disabled)
        {
            return $vars;
        }
        $prop = $vars['property'];
        
        $translation = static::lookup_translation($entity, $prop, $lang, $origLang, $translateMode);        
        
        if ($origLang && $origLang != $lang)
        {
            PageContext::set_original_language($origLang);                                    
        }
        
        PageContext::add_available_translation($translation);

        if ($translateMode != TranslateMode::None)
        {
            $vars['value'] = $translation->value;
        }
        
        return $vars;
    }

    private static function lookup_translation($entity, $prop, $lang, $origLang, $translateMode)
    {
        $key = static::get_translation_key($entity, $prop, $lang);
        if (!$key->guid)
        {
            try
            {
                $key->save();
            }
            catch (DatabaseException $ex) 
            {
                // another visitor concurrently created this key; 
                // unique key constraint violated
                $tempTrans = $key->new_translation();
                $tempTrans->value = $entity->$prop;
                $tempTrans->source = Translation::Original;
                return $tempTrans;
            }
        }
        
        $approvedTrans = $key->query_translations()
            ->where('approval > 0')
            ->order_by('approval_time desc, tid desc')
            ->get();

        $doAutoTranslate = ($translateMode == TranslateMode::Automatic) 
            && ($origLang != $lang)
            && GoogleTranslate::is_supported_language($origLang)
            && GoogleTranslate::is_supported_language($lang);        
        
        if ($doAutoTranslate && (!$approvedTrans || $approvedTrans->is_stale()))
        {
            $autoTrans = $key->query_translations()
                ->where('source = ?', Translation::GoogleTranslate)
                ->order_by('time_created desc, tid desc')
                ->get();
        
            if ($autoTrans && !$autoTrans->is_stale())
            {
                return $autoTrans;
            }
            else if (!Request::is_bot())
            {         
                $key->queue_auto_translation();
                
                $tempTrans = $key->new_translation();
                $tempTrans->value = __('trans:translating', $lang);
                $tempTrans->source = Translation::GoogleTranslate;
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
            $tempTrans->value = $entity->$prop;
            $tempTrans->source = Translation::Original;
            return $tempTrans;
        }
    }    
    
    private static function get_translation_key($entity, $prop, $lang)
    {
        $guid = $entity->guid;
        $key_name = "{$guid}:{$prop}";
        
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