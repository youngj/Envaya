<?php

class Translation extends ElggObject
{
    static $subtype_id = T_translation;
    static $table_name = 'translations';
    static $table_attributes = array(
        'hash' => '',
        'property' => '',
        'lang' => '',
        'value' => ''
    );
    
    public function save()
    {
        $this->hash = $this->calculateHash();
        return parent::save();
    }
    
    public function getOriginalText()
    {
        $obj = $this->getContainerEntity();
        $property = $this->property;
        return trim($obj->$property);
    }
    
    public function calculateHash()
    {
        return $this->getRootContainerEntity()->language . ":" . sha1($this->getOriginalText());        
    }    
    
    public function isStale()
    {
        return $this->calculateHash() != $this->hash;
    }
}

class InterfaceTranslation extends ElggObject
{
    static $subtype_id = T_interface_translation;
    static $table_name = 'interface_translations';
    static $table_attributes = array(
        'key' => '',
        'lang' => '',
        'value' => '',
        'approval' => 0
    );   
    
    static function getByKeyAndLang($key, $lang)
    {
        return static::getByCondition(array('`key` = ?', 'lang = ?'), array($key, $lang));
    }
    
    static function filterByLang($lang)
    {
        return static::filterByCondition(array('lang = ?'), array($lang));
    }
}


class TranslateMode
{
    const None = 1;
    const ManualOnly = 2;
    const All = 3;    
}

function translate_field($obj, $field)
{        
    $text = trim($obj->$field);
    if (!$text)
    {
        return '';
    }   

    $org = $obj->getRootContainerEntity();
    if (!($org instanceof Organization))
    {
        return '';
    }
    
    $origLang = $org->language;
    $viewLang = get_language();

    if ($origLang != $viewLang)
    {
        global $CONFIG;
        
        if (!isset($CONFIG->translations_available))
        {
            $CONFIG->translations_available = array('origlang' => $origLang, 'properties' => array());
        }

        $translateMode = get_translate_mode();
        $translation = lookup_translation($obj, $field, $origLang, $viewLang, $translateMode);            
        
        if ($obj->canEdit())
        {
            $CONFIG->translations_available['translatable_props'][] = array($obj->guid, $field);
        }    
        
        if ($translation && $translation->owner_guid)
        {
            $CONFIG->translations_available[TranslateMode::ManualOnly] = true;            
            
            if ($translation->isStale())
            {
                $CONFIG->translations_available['stale'] = true;
            }
            
            $viewTranslation = ($translateMode > TranslateMode::None);
        }
        else
        {
            $CONFIG->translations_available[TranslateMode::All] = true;
            $viewTranslation = ($translateMode == TranslateMode::All);
        }

        if ($viewTranslation && $translation)
        {
            return $translation->value;
        }
        else
        {
            return $obj->$field;
        }
    }   

    return $text;
}

function lookup_translation($obj, $prop, $origLang, $viewLang, $translateMode = TranslateMode::ManualOnly)
{
    $where = array();
    $args = array();

    $where[] = "subtype=?";
    $args[] = T_translation;

    $where[] = "property=?";
    $args[] = $prop;

    $where[] = "lang=?";
    $args[] = $viewLang;

    $where[] = "container_guid=?";
    $args[] = $obj->guid;

    $entities = get_entities_by_condition('translations', $where, $args, '', 1);          
    
    $doAutoTranslate = ($translateMode == TranslateMode::All);
    
    if (!empty($entities)) 
    {        
        $trans = $entities[0];
        
        if ($doAutoTranslate && $trans->isStale())
        {
            $text = get_auto_translation($obj->$prop, $origLang, $viewLang);
            if ($text != null)
            {
                if (!$trans->owner_guid) // previous version was from google
                {            
                    $trans->value = $text;
                    $trans->save();
                }
                else // previous version was from human
                {
                    // TODO : cache this
                    $fakeTrans = new Translation();    
                    $fakeTrans->owner_guid = 0;
                    $fakeTrans->container_guid = $obj->guid;
                    $fakeTrans->property = $prop;
                    $fakeTrans->lang = $viewLang;
                    $fakeTrans->value = $text;                               
                    return $fakeTrans;
                }        
            }    
        }    
        
        return $trans;
    }
    else if ($doAutoTranslate)
    {   
        $text = get_auto_translation($obj->$prop, $origLang, $viewLang);
        
        if ($text != null)
        {
            $trans = new Translation();    
            $trans->owner_guid = 0;
            $trans->container_guid = $obj->guid;
            $trans->property = $prop;
            $trans->lang = $viewLang;
            $trans->value = $text;            
            $trans->save();
            return $trans;
        }    
        return null;
    }
    return null;
}

function get_auto_translation($text, $origLang, $viewLang)
{
    if ($origLang == $viewLang)
    {
        return null;
    }    

    $text = trim($text);
    if (!$text)
    {
        return null;
    }
           
    $ch = curl_init(); 
    
    $text = str_replace("\r","", $text);
    $text = str_replace("\n", ",;", $text);
    
    $url = "ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$origLang%7C$viewLang&q=".urlencode($text);
    
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_REFERER, "www.envaya.org");     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    
    $json = curl_exec($ch); 
         
    curl_close($ch);     
    
    $res = json_decode($json);
                
    $translated = $res->responseData->translatedText;
    if (!$translated)
    {
        return null;
    }
            
    $text = html_entity_decode($translated, ENT_QUOTES);
    
    return str_replace(",;", "\n", $text);   
}

function get_translate_mode()
{
    return ((int)get_input("trans")) ?: TranslateMode::ManualOnly;
}

function set_translate_mode($trans)
{
    set_input('trans', $trans);
}

function get_original_language()
{
    global $CONFIG;
    if (isset($CONFIG->translations_available))
    {
        return $CONFIG->translations_available['origlang'];
    }
    
    return '';
}


function page_translatable_properties()
{
    global $CONFIG;
    if (isset($CONFIG->translations_available))
    {
        return $CONFIG->translations_available['translatable_props'];
    }    
    return array();
}

function page_has_stale_translation()
{
    global $CONFIG;
    return (isset($CONFIG->translations_available) && isset($CONFIG->translations_available['stale']));    
}

function page_is_translatable($mode=null)
{
    global $CONFIG;
    global $PAGE_TRANSLATABLE;
    
    if (isset($PAGE_TRANSLATABLE) && !$PAGE_TRANSLATABLE)
    {
        return false;
    }
    
    if (isset($CONFIG->translations_available))
    {
        if ($mode == null || isset($CONFIG->translations_available[$mode]))
        {
            return true;
        }
    }
    return false;
}

function page_set_translatable($translatable)
{
    global $PAGE_TRANSLATABLE;
    $PAGE_TRANSLATABLE = $translatable;
}