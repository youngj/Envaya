<div class='adminBox'>
        <?php 
        
        $org = $vars['entity'];

        if ($org->approval == 0) 
        { 
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('org:approve'), 
                'is_action' => true,
                'href' => "action/org/approve?org_guid={$org->guid}&approval=2"
            ));   
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('org:reject'), 
                'is_action' => true,
                'href' => "action/org/approve?org_guid={$org->guid}&approval=-1"
            ));                
        }
        else 
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo($org->approval > 0 ? 'org:unapprove' : 'org:unreject'), 
                'is_action' => true,
                'href' => "action/org/approve?org_guid={$org->guid}&approval=0"
            ));        
        }

        if ($org->approval < 0)
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('org:delete'), 
                'is_action' => true,
                'href' => "action/entities/delete?guid={$org->guid}"
            ));            
        }    

                
        ?>
</div>        