<?php    
	global $CONFIG;
	
    gatekeeper();
	action_gatekeeper();
	
    $user_id = get_input('guid');    
    $org = get_entity($user_id);

    if ($org && $org instanceof Organization && $org->canEdit())
    {
        if (get_input('deleteicon'))
        {
            $org->custom_icon = false;
            $org->save();
            
            system_message(elgg_echo("org:icon:reset"));
        }
        if (has_uploaded_file('icon'))
        {
            if (!is_image_upload('icon'))
            {                
                register_error(elgg_echo('upload:invalid_image'));
            }
            else
            {   
                $org->setIcon(get_uploaded_filename('icon'));
            }    
        }
        
        $theme = get_input('theme');
        
        if ($theme != $org->theme)
        {
            system_message(elgg_echo("org:theme:changed"));
            $org->theme = $theme;
            $org->save();
        }    
    }    
	
?>