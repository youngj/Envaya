<?php

    $org = $vars['org'];
    $loggedInOrg = Session::get_loggedin_user();

    if ($loggedInOrg instanceof Organization)
    {
        $controls = array();
        if ($org->email && $loggedInOrg->is_approved())
        {
            $controls[] = "<a href='{$org->get_url()}/send_message'>".__('message:link')."</a>";
        }
        
        $networkPage = $loggedInOrg->get_widget_by_class('Network');
        
        if ($loggedInOrg->query_relationships()->where('subject_guid = ?', $org->guid)->is_empty())
        {        
            /*
            $controls[] = view('widgets/network_add_relationship_link', array(
                'widget' => $networkPage, 
                'org' => $org, 
                'type' => OrgRelationship::Membership
            ));
            */
            
            $controls[] = view('widgets/network_add_relationship_link', array(
                'widget' => $networkPage, 
                'org' => $org, 
                'type' => OrgRelationship::Partnership
            ));
        }
            
        if (sizeof($controls))
        {
            echo "<table class='commBox'><tr><td class='commBoxLeft'>&nbsp;</td>";
            
            foreach ($controls as $control)
            {
                echo "<td class='commBoxMain'>$control</td>";
            }
            
            echo "<td class='commBoxRight'>&nbsp;</td></table>";
        }
    }
    
    