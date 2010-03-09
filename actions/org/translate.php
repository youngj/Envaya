<?php

    gatekeeper();
    action_gatekeeper();

    $text = get_input('translation');
    $guid = get_input('entity_guid');
    $property = get_input('property');  
    $entity = get_entity($guid);
    
    if (!isadminloggedin() || !$entity)
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }    
    else if (empty($text)) 
    {
        register_error(elgg_echo("trans:empty"));
        forward_to_referrer();
    }    
    else 
    {    
        $lang = get_language();
        $trans = lookup_translation($entity, $property, $org->language, $lang);    
        if (!$trans)
        {   
            $trans = new Translation();    
            $trans->container_guid = $entity->guid;
            $trans->property = $property;
            $trans->lang = $lang;
        }            
        $trans->owner_guid = get_loggedin_userid();
        $trans->value = $text;            
        $trans->save();
        
        system_message(elgg_echo("trans:posted"));
        
        forward($entity->getUrl());                    
    }
   
?>
