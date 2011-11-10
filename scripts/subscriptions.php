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

function get_subscription_class($type)
{
    $cls = ClassRegistry::get_class($type);
    
    if (!$cls)
    {
        die("Unknown type id $type");
    }
    return $cls;
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

function delete_subscription($guid)
{
    $subscription = Subscription::get_by_guid($guid);
    if (!$subscription)
    {
        die("Subscription $guid does not exist");
    }
    
    //echo "$permission\n";
    $subscription->delete();
    echo "Subscription {$subscription->guid} deleted.\n";
}

function print_opts($opts)
{
    $type_str = @$opts['type'] ?: '<any>';
    $email_str = @$opts['email'] ?: '<any>';
    $scope_str = @$opts['scope'] ?: '<any>';    
    
    echo "\n";
    echo "type     = {$type_str}\n";
    echo "email    = {$email_str}\n";    
    echo "scope    = {$scope_str}\n\n";
}

function add_subscription($opts)
{
    if (!@$opts['type'])
    {
        die("Missing subscription type");
    }

    if (!@$opts['email'])
    {
        die("Missing email");
    }
    
    $cls = get_subscription_class($opts['type']);    
    $user = get_user(@$opts['username']);
    $email = EmailAddress::validate(@$opts['email']);
    $scope = get_scope(@$opts['scope']);
    
    print_opts($opts);
    
    $subscription = $cls::init_for_entity($scope, $email, array(
        'owner_guid' => $user ? $user->guid : 0,
        'language' => @$opts['lang'] ?: Config::get('language')
    ));
        
    echo "Subscription {$subscription->guid} added.\n";
}

function usage()
{
    global $argv;
    echo "\nUsage:\n\n";
    
    echo "Add subscription\n";
    echo "-a --type=<type> --scope=<scope> --email=<email> [--username=<username>]\n\n";
           
    echo "Delete subscription\n";
    echo "-d <id>\n\n";

    echo "Valid scopes: 'root' or entity guid (see /admin/entities)\n\n";
}

function main()
{        
    $opts = getopt('had:',array("type:","scope:","email:","username:","lang:"));
    
    if (isset($opts['d']))
        return delete_subscription(@$opts['d']);
    
    if (isset($opts['a']))
        return add_subscription($opts);    
        
    return usage();
}

main();