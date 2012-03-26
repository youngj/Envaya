<?php
    require_once "start.php";      
    
    $metadatas = EntityMetadata::query()->order_by('id')->filter();
        
    $ignored = array('uuid', 'notification:method:email','passwd_conf_code','migrated_custom_header','migrated_custom_icon',
        'login_failure_14','login_failure_13','login_failure_12','login_failure_11','login_failure_10','login_failure_9',
        'login_failure_8','login_failure_7','login_failure_6','login_failure_5','language','hander_class','invited_emails',
        'notify_days','created_by_guid','admin_created','image_position','is_html','included','local','custom_header'
    );
    foreach ($metadatas as $metadata)
    {
        if (!in_array($metadata->name, $ignored))
        {
            $entity = Entity::get_by_guid($metadata->entity_guid);
            if ($entity)
            {
                $entity->set_metadata($metadata->name, $metadata->value);
                $entity->save();
            
                error_log("{$metadata->id}");
            }
        }
    }
