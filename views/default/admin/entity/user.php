<?php
    $user = $vars['entity'];   
            
    echo "Approval: ";
    echo $user->is_approved() ? "Approved" : 
        ($user->approval == User::Rejected ? "Rejected" : "Pending");        
    echo "<br />";
    
    $permissions = $user->get_all_permissions();        
    if ($permissions)
    {
        echo "<br />";
        echo "<h4>Own Permissions (".sizeof($permissions).")</h4>";
        
        $container_groups = array();
        $subtype_groups = array();
        
        foreach ($permissions as $permission)
        {
            $container = $permission->get_container_entity();
            $title_html = escape($permission->get_title());
            
            if ($container)
            {
                $container_html = "<a href='{$container->get_admin_url()}'>".escape($container->get_title())."</a>";
            }
            else
            {
                $container_html = $permission->container_guid;
            }            
            $container_groups[$container_html][] = $title_html;
            $subtype_groups[$title_html][] = $container_html;
        }        
        
        echo view('admin/item_groups', array('item_groups' => $container_groups));        
        echo "<br />";
        echo view('admin/item_groups', array('item_groups' => $subtype_groups));
        echo "<br />";
    }        
    