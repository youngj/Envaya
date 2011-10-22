<?php
    require_once "start.php";      
    
    foreach (Organization::query()
        ->filter() as $org)
    {        
        $email = $org->email;
        
        if (!$email)
        {
            continue;
        }
        
        echo "{$org->username}: {$email}";
        
        $defaults = array('owner_guid' => $org->guid, 'language' => $org->language);
    
        if ($org->notifications & 1)
        {
            echo " B";
            EmailSubscription_Contact::init_for_entity($org, $email, $defaults);
        }
        
        if ($org->notifications & 2)
        {
            echo " C";
            EmailSubscription_Comments::init_for_entity($org, $email, $defaults);
        }
        
        if ($org->notifications & 4)
        {
            echo " N";
            EmailSubscription_Network::init_for_entity($org, $email, $defaults);
        }
        
        if ($org->notifications & 8)
        {
            echo " D";
            EmailSubscription_Discussion::init_for_entity($org, $email, $defaults);
        }
        echo "\n";
    }