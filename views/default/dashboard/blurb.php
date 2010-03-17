<?php

    $user = get_loggedin_user();
    
    if ($user instanceof Organization)
    {
        echo elgg_view("org/dashboard", array('org' => $user));
    }
    else
    {
        echo "<div class='padded'>You are not an organization!</div>";
    }
        

?>