<?php

    global $CONFIG;   
    
    $user = get_loggedin_user();
    
    if ($user instanceof Organization)
    {
        echo "You are an organization!";
    }
    else
    {
        echo "You are a regular user!";
    }
        

?>