<?php

    gatekeeper();
    action_gatekeeper();

    $body = get_input('blogbody');
    $orgId = get_input('container_guid');
    $org = get_entity($orgId);
    
    $imageFiles = get_uploaded_files($_POST['image']);
            
    if (empty($body) && !$imageFiles) 
    {
        register_error(elgg_echo("blog:blank"));
        forward_to_referrer();
    } 
    else if (!$org->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else 
    {   
        $blog = new NewsUpdate();
        $blog->owner_guid = get_loggedin_userid();
        $blog->container_guid = $orgId;
        $blog->content = $body;    
        $blog->save();
        
        $blog->setImages($imageFiles);        

        system_message(elgg_echo("blog:posted"));
            
        $page_owner = get_entity($blog->container_guid);
        forward($page_owner->getUrl() . "/news");
    }       