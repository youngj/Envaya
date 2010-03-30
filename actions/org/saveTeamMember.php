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
        
        if (has_uploaded_file('image'))
        {   
            if (is_image_upload('image'))
            {
                $member->setImage(get_uploaded_filename('image'));        
            }   
            else
            {
                register_error(elgg_echo('upload:invalid_image'));
            }
        }        
        else if (get_input('deleteimage'))
        {
            $member->setImage(null);
        }                
        $member->save();
        
        system_message(elgg_echo('widget:team:save_success'));
        
        $widget = $org->getWidgetByName('team');        
        forward($widget->getEditURL());
    }
