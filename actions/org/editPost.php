<?php

    gatekeeper();
    action_gatekeeper();

    $guid = (int) get_input('blogpost');
    $body = get_input('blogbody');
    $blog = get_entity($guid);    
    
    $hasImage = has_uploaded_file('image');
    
    if ($blog->getSubtype() != T_blog || !$blog->canEdit()) 
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else if (empty($body) && !$hasImage && !$blog->hasImage()) 
    {
        register_error(elgg_echo("blog:blank"));
        forward_to_referrer();
    }    
    else if (get_input('delete'))
    {
        $org = $blog->getContainerEntity();
        $blog->disable('', $recursive=false);     
        system_message(elgg_echo('blog:delete:success'));            
        forward($org->getURL()."/news");
    }        
    else 
    {
        $blog->access_id = ACCESS_PUBLIC;
        $blog->content = $body;

        if (!$blog->save()) 
        {
            register_error(elgg_echo("blog:error"));
            forward();
        }
        
        if ($hasImage)
        {   
            if (is_image_upload('image'))
            {
                $blog->setImage(get_uploaded_file('image'));        
            }   
            else
            {
                register_error(elgg_echo('upload:invalid_image'));
            }
        }        
        else if (get_input('deleteimage'))
        {
            $blog->setImage(null);
        }        

        system_message(elgg_echo("blog:updated"));
        forward($blog->getUrl());                    
    }
   
?>
