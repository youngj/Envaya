<?php
    gatekeeper();
    action_gatekeeper();    

    $guid = (int) get_input('blogpost');
    $blog = get_entity($guid);
        
    if ($blog->getSubtype() == T_blog && $blog->canEdit()) 
    {
        $redirectUrl = $blog->getContainerEntity()->getUrl() ."/news/edit";
        $owner = get_entity($blog->getOwner());        
        $blog->delete();        
        system_message(elgg_echo("blog:deleted"));        
        forward($redirectUrl);
    }       
?>