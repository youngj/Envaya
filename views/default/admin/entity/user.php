<?php
    $user = $vars['entity'];   
            
    echo "Approval: ";
    echo $user->is_approved() ? "Approved" : 
        ($user->approval == User::Rejected ? "Rejected" : "Pending");        