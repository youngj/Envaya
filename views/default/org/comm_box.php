<div class='commBox'>
    
        <?php 
        
        $org = $vars['entity'];
        $loggedInOrg = get_loggedin_user();
        
        $pInfo = Partnership::getPartnership($org->guid, $loggedInOrg->guid);

        if(!$pInfo)
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('org:partner'),
                'is_action' => true,
                'href' => "action/org/requestPartner?org_guid={$org->guid}"
            ));
        }
        else if(!$pInfo->p2Approved)
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('org:partnerRequestedMustApprove'),
                'is_action' => true,
                'href' => Partnership::generatePartnerApproveUrl($loggedInOrg->guid, $org->guid)
            ));
        }
        else if(!$pInfo->p1Approved)
        {
            echo elgg_echo('org:waitingPartnerApprove');
        }
        else
        {
            echo elgg_echo('org:partnershipExists');
        }     
        ?>
        
</div>