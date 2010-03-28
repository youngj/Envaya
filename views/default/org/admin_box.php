<div class='adminBox'>
        <?php 
        
        $org = $vars['entity'];

        if ($org->approval == 0) 
        { 
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('approval:approve'), 
                'is_action' => true,
                'href' => "action/org/approve?org_guid={$org->guid}&approval=2"
            ));   
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('approval:reject'), 
                'is_action' => true,
                'href' => "action/org/approve?org_guid={$org->guid}&approval=-1"
            ));                
        }
        else 
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo($org->approval > 0 ? 'approval:unapprove' : 'approval:unreject'), 
                'is_action' => true,
                'href' => "action/org/approve?org_guid={$org->guid}&approval=0"
            ));        
        }

        if ($org->approval < 0)
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('approval:delete'), 
                'is_action' => true,
                'href' => "action/entities/delete?guid={$org->guid}"
            ));            
        }    

                
        ?>
</div>        