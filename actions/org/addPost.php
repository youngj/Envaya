<?php

    gatekeeper();
    action_gatekeeper();

    $body = get_input('blogbody');
    $orgId = get_input('container_guid');
    $org = get_entity($orgId);
    
    $hasImage = (isset($_FILES['image'])) && (substr_count($_FILES['image']['type'],'image/'));
            
    if (empty($body) && !$hasImage) 
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
        $blog->owner_guid = $_SESSION['user']->getGUID();
        $blog->container_guid = $orgId;
        $blog->content = $body;
    
        if (!$blog->save()) 
        {
            register_error(elgg_echo("blog:error"));
            forward_to_referrer();
        }

        if ($hasImage)
        {        
            $blog->setImage(get_uploaded_filename('image'));        
        }

        system_message(elgg_echo("blog:posted"));
            
        $page_owner = get_entity($blog->container_guid);
        forward($page_owner->getUrl() . "/news");
    }
        
?>
