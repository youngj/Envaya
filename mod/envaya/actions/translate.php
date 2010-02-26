<?php

    gatekeeper();
    action_gatekeeper();

    $translation = get_input('translation');
    $metaDataId = get_input('metadata_id');
    $metaData = get_metadata($metaDataId);
    $orgId = get_input('org_id');
    $org = get_entity($orgId);
        
    if (!$metaData || !$org) 
    {
        register_error(elgg_echo("trans:invalid_id"));
        forward_to_referrer();
    }
    else if (empty($translation)) 
    {
        register_error(elgg_echo("trans:empty"));
        forward_to_referrer();
    }    
    else 
    {    
        $origText = get_metastring($metaData->value_id);
        $key = get_translation_key($origText, $org->language, get_language());
    
        $trans = new Translation();    
        $trans->owner_guid = get_loggedin_userid();        
        $trans->container_guid = 0;
        $trans->access_id = 2; //public
        $trans->save();
        $trans->key = $key;
        $trans->text = $translation;

        system_message(elgg_echo("trans:posted"));
                
        $metaEntity = get_entity($metaData->entity_guid);
                
        forward($metaEntity->getUrl());                    
    }
   
?>
