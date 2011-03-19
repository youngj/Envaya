<?php

/*
 * Rewrites email addresses for all users in the database.
 * Makes sure that users won't be emailed from the test server,
 * when testing with production data
 */

require_once("scripts/cmdline.php");
require_once("engine/start.php");

if (Config::get('debug') && Config::get('domain') != 'envaya.org')
{
    $users = User::query()->filter();

    $admin_email = Config::get('admin_email');
    
    foreach ($users as $user)
    {
        echo "{$user->email} -> ";        
        $user->email = str_replace('@',"+{$user->username}@", $admin_email);
        echo "{$user->email}\n";                
        $user->save();
    }
}