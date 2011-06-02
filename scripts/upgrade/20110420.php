<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");
    
    $metadatas = EntityMetadata::query()->where('name = ?', 'thumbnail_url')->filter();
    
    foreach ($metadatas as $metadata)
    {
        $thumbnail_url = $metadata->value;
        if (!$thumbnail_url)
            continue;
        
        $entity = Entity::get_by_guid($metadata->entity_guid, true);
        if (!$entity)
            continue;

        echo get_class($entity) . " {$entity->guid} {$thumbnail_url}\n";        
        $entity->thumbnail_url = $thumbnail_url;
        $entity->save();
        //$metadata->delete();
    }