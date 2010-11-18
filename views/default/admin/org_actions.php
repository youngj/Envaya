<div class='adminBox'>
<?php

$org = $vars['entity'];

if ($org->approval == 0)
{
    echo view('output/confirmlink', array(
        'text' => __('approval:approve'),
        'is_action' => true,
        'href' => "admin/approve?org_guid={$org->guid}&approval=2"
    ));
    echo " ";
    echo view('output/confirmlink', array(
        'text' => __('approval:reject'),
        'is_action' => true,
        'href' => "admin/approve?org_guid={$org->guid}&approval=-1"
    ));
    echo " ";
}
else
{
    echo view('output/confirmlink', array(
        'text' => __($org->approval > 0 ? 'approval:unapprove' : 'approval:unreject'),
        'is_action' => true,
        'href' => "admin/approve?org_guid={$org->guid}&approval=0"
    ));
    echo " ";
}

if ($org->approval < 0)
{
    echo view('output/confirmlink', array(
        'text' => __('approval:delete'),
        'is_action' => true,
        'href' => "admin/delete_entity?guid={$org->guid}&next=/admin/user"
    ));
    echo " ";
}

echo "<a href='/admin/add_featured?username={$org->username}'>".__('featured:add')."</a>";        
echo " ";
echo "<a href='/{$org->username}/dashboard'>".__('dashboard:title')."</a>";
echo " ";
echo "<a href='/{$org->username}/settings'>".__('help:settings')."</a>";
echo " ";
echo "<a href='/{$org->username}/username'>".__('username:title')."</a>";
echo " ";
echo "<a href='/{$org->username}/domains'>".__('domains:edit')."</a>";
echo " ";

echo get_submenu_group('org_actions', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group'); 

?>
</div>