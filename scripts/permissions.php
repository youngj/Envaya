<?php

$root = dirname(__DIR__);

require_once "$root/scripts/cmdline.php";
require_once "$root/start.php";

function get_user($username)
{
    if (!$username)
        return null;
        
    $user = User::get_by_username($username);
    if (!$user)
    {
        die("Unknown username $username\n");
    }
    return $user;
}

function get_permission_class($type)
{
    if ($type)
    {
        $cls = EntityRegistry::get_subtype_class($type);
        
        if (!$cls)
        {
            die("Unknown type id $type");
        }
        return $cls;
    }
    else
    {
        return 'Permission';
    }
}

function get_scope($scope_id)
{
    if ($scope_id == 'root')
    {
        $scope = UserScope::get_root();
    }
    else
    {
        $scope = Entity::get_by_guid($scope_id);
    }
   
    if (!$scope)
    {
        die("Unknown scope $scope");
    }   
    
    return $scope;
}

function list_permissions($opts)
{
    $type = @$opts['type'];
    $cls = get_permission_class($type);    
    $query = $cls::query();
    
    $username = @$opts['username'];
    if ($username)
    {
        $query->where('owner_guid = ?', get_user($username)->guid);
    }
    
    $scope_id = @$opts['scope'];
    if ($scope_id)
    {
        $container_guids = array();
        $cur = $scope = get_scope($scope_id);        
        while ($cur)
        {
            $container_guids[] = $cur->guid;
            $cur = $cur->get_container_entity();
        }        
    
        $query->where_in('container_guid', $container_guids);
    }

    print_opts($opts);    
    
    $count = $query->count();    
    
    echo "$count permissions\n";
    
    foreach ($query->filter() as $permission)
    {
        echo "$permission\n";
    }
}

function delete_permission($guid)
{
    $permission = Permission::get_by_guid($guid);
    if (!$permission)
    {
        die("Permission $guid does not exist");
    }
    
    echo "$permission\n";
    $permission->delete();
    echo "Permission deleted.\n";
}

function print_opts($opts)
{
    $type_str = @$opts['type'] ?: '<any>';
    $username_str = @$opts['username'] ?: '<any>';
    $scope_str = @$opts['scope'] ?: '<any>';    
    
    echo "\n";
    echo "type     = {$type_str}\n";
    echo "username = {$username_str}\n";
    echo "scope    = {$scope_str}\n\n";
}

function test_permission($opts)
{
    $cls = get_permission_class(@$opts['type']);    
    $user = get_user(@$opts['username']);

    print_opts($opts);
    
    if (@$opts['scope'])
    {
        $scope = get_scope(@$opts['scope']);
        $granted = ($cls::is_granted($scope, $user));
    }
    else
    {
        $granted = ($cls::is_any_granted($user));
    }
    
    if ($granted)
    {
        echo "YES\n";
        
        if ($opts['scope'])
        {
            $permission = $cls::get_explicit($scope, $user);
        }
        else
        {
            $permission = $cls::get_any_explicit($user);
        }
        
        if ($permission)
        {
            echo "$permission\n";
        }
        else
        {
            echo "(implicit)\n";
        }
    }    
    else
    {
        echo "NO";
    }
}

function add_permission($opts)
{
    if (!@$opts['type'])
    {
        die("Missing permission type");
    }

    if (!@$opts['username'])
    {
        die("Missing username");
    }
    
    $cls = get_permission_class($opts['type']);    
    $user = get_user(@$opts['username']);
    $scope = get_scope(@$opts['scope']);
    
    print_opts($opts);
    
    $permission = $cls::get_explicit($scope, $user);
    
    if ($permission)
    {
        die("User already has this permission for this scope:\n$permission\n");
    }
    
    $permission = $cls::grant_explicit($scope, $user);
    
    echo "Permission granted.\n";
    echo "$permission\n";
}

function usage()
{
    global $argv;
    echo "\nUsage:\n\n";
    
    echo "List permissions\n";
    echo "-l [--type=<type>] [--scope=<scope>] [--username=<username>]\n\n";

    echo "Add permission\n";
    echo "-a --type=<type> --scope=<scope> --username=<username>\n\n";
           
    echo "Delete permission\n";
    echo "-d <id>\n\n";
    
    echo "Test permission\n";
    echo "-t --type=<type> --username=<username> [--scope=<scope>]\n\n";
    
    echo "Valid scopes: 'root' or entity guid (see /admin/entities)\n\n";
}

function main()
{        
    $opts = getopt('hlatd:',array("type:","scope:","username:"));
    
    if (isset($opts['t']))
        return test_permission($opts);
    
    if (isset($opts['d']))
        return delete_permission(@$opts['d']);
    
    if (isset($opts['a']))
        return add_permission($opts);
    
    if (isset($opts['l']))
        return list_permissions($opts);
        
    return usage();
}

main();