<?php

/*
 * Rewrites email addresses for all users in the database.
 * Makes sure that users won't be emailed from the test server,
 * when testing with production data
 */

require_once("scripts/cmdline.php");
require_once("start.php");

if (Config::get('debug') && Config::get('domain') != 'envaya.org')
{
    $users = User::query()->filter();

    $admin_email = Config::get('mail:admin_email');
    
    foreach ($users as $user)
    {
        echo "{$user->email} -> ";        
        $user->email = str_replace('@',"+{$user->username}@", $admin_email);
        echo "{$user->email}\n";                
        $user->save();
    }
    
    foreach (EmailSubscription::query()->filter() as $subscription)
    {
        $subscription->email = $admin_email;
    }
    
    // also disable sms subscriptions
    foreach (SMSSubscription::query()->filter() as $subscription)
    {
        $subscription->disable();
        $subscription->save();
    }
}
else
{
    echo "not in debug mode";
}