<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");
    
    $rows = Database::get_rows("SELECT * FROM translations");
    
    foreach ($rows as $row)
    {
        $language = TranslationLanguage::get_by_code($row->lang);
        
        try
        {
            $entity = Entity::get_by_guid($row->container_guid);
        }
        catch (InvalidParameterException $ex)
        {
            continue;
        }
        if (!$entity)
        {
            continue;
        }
        
        $property = $row->property;
        
        $key_name = "entity:{$entity->guid}:{$property}";
        
        $key = $language->query_keys()->where('name = ?', $key_name)->get();
        if (!$key)
        {
            $key = new EntityTranslationKey();
            $key->name = $key_name;
            $key->container_guid = $entity->guid;
            $key->language_guid = $language->guid;
            $key->save();            
        }
        
        $cur_value = $entity->$property;
        if (md5($cur_value) == $row->hash)
        {
            $translation->default_value_hash = sha1($cur_value);
        }        
        
        $translation = $key->query_translations()->where('value = ?', $row->value)->get();
        if ($translation)
        {
            continue;
        }
        
        $translation = $key->new_translation();
        $translation->time_created = $row->time_updated;
        $translation->value = $row->value;
        $translation->owner_guid = $row->owner_guid;
        $translation->save();
        $key->update();
        
        echo "{$translation->get_url()}\n";
    }
