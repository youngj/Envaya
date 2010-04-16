<?php

    gatekeeper();
    action_gatekeeper();

    $memberId = (int)get_input('member_guid');
    $member = get_entity($memberId);
    
    if (!$member || !$member->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else 
    {   
        $org = $member->getContainerEntity();        
        
        $member->name = get_input('name');
        $member->description = get_input('description');
        
        $imageFiles = get_uploaded_files($_POST['image']);        
        if (get_input('deleteimage'))
        {
            $member->setImages(null);
        }                
        else if ($imageFiles)
        {   
            $member->setImages($imageFiles);        
        }        
        $member->save();
        
        system_message(elgg_echo('widget:team:save_success'));
        
        $widget = $org->getWidgetByName('team');        
        forward($widget->getEditURL());
    }
