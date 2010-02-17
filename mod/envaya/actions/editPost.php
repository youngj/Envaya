<?php

    gatekeeper();
    action_gatekeeper();

    $guid = (int) get_input('blogpost');
    $body = get_input('blogbody');

    $blog = get_entity($guid);
    if ($blog->getSubtype() == "blog" && $blog->canEdit()) 
    {
        if (empty($body)) 
        {
            register_error(elgg_echo("blog:blank"));
            forward();
        }    
        else 
        {
            $owner = get_entity($blog->getOwner());
            $blog->access_id = 2;
            $blog->description = $body;

            if (!$blog->save()) 
            {
                register_error(elgg_echo("blog:error"));
                forward();
            }
              
            system_message(elgg_echo("blog:posted"));
            forward($blog->getUrl());                    
        }
    }
        
?>
