<div class='adminBox'>
        <?php

        $org = $vars['entity'];

        if ($org->approval == 0)
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('approval:approve'),
                'is_action' => true,
                'href' => "admin/approve?org_guid={$org->guid}&approval=2"
            ));
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('approval:reject'),
                'is_action' => true,
                'href' => "admin/approve?org_guid={$org->guid}&approval=-1"
            ));
        }
        else
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo($org->approval > 0 ? 'approval:unapprove' : 'approval:unreject'),
                'is_action' => true,
                'href' => "admin/approve?org_guid={$org->guid}&approval=0"
            ));
        }

        if ($org->approval < 0)
        {
            echo elgg_view('output/confirmlink', array(
                'text' => elgg_echo('approval:delete'),
                'is_action' => true,
                'href' => "admin/delete_entity?guid={$org->guid}"
            ));
        }

        echo "<a href='{$org->username}/dashboard'>".elgg_echo('dashboard')."</a>";
        echo "<a href='{$org->username}/settings'>".elgg_echo('help:settings')."</a>";
        echo "<a href='{$org->username}/username'>".elgg_echo('username:title')."</a>";

        ?>
</div>