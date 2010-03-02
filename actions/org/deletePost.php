<?php
    gatekeeper();
    action_gatekeeper();    

    $guid = (int) get_input('blogpost');
    $blog = get_entity($guid);
        
    if ($blog->getSubtype() == "blog" && $blog->canEdit()) 
    {
        $redirectUrl = $blog->getContainerEntity()->getUrl() ."/news";
        $owner = get_entity($blog->getOwner());
        $rowsaffected = $blog->delete();
        if ($rowsaffected > 0) 
        {
            system_message(elgg_echo("blog:deleted"));
        } 
        else 
        {
            register_error(elgg_echo("blog:notdeleted"));
        }
        forward($redirectUrl);
    }       
?>