<?php   

    action_gatekeeper();    

    $user_id = get_input('guid');    
    $org = get_entity($user_id);

    if ($org && $org instanceof Organization && $org->canEdit())
    {
        $theme = get_input('theme');

        if ($theme != $org->theme)
        {
            system_message(elgg_echo("theme:changed"));
            $org->theme = $theme;
            $org->save();            
        }    
        
        $iconFiles = get_uploaded_files($_POST['icon']);

        if (get_input('deleteicon'))
        {
            $org->setIcon(null);       
            system_message(elgg_echo("icon:reset"));
        }
        else if ($iconFiles)
        {
            $org->setIcon($iconFiles);
            system_message(elgg_echo("icon:saved"));
        }   
        
        $headerFiles = get_uploaded_files($_POST['header']);
        
        if (get_input('deleteheader'))
        {
            $org->setHeader(null);       
            system_message(elgg_echo("header:reset"));
        }
        else if ($headerFiles)
        {
            $org->setHeader($headerFiles);
            system_message(elgg_echo("header:saved"));
        }           
    }
    
    forward($org->getURL());