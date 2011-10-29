<?php

return function() {
    $root_scope = UserScope::get_root();
    if (!$root_scope)
    {    
        $root_scope = new UserScope();
        $root_scope->save();
    }

    $admin_email = Config::get('admin_email');
    
    EmailSubscription_Comments::init_for_entity($root_scope, $admin_email);
    
    $admin = get_or_create_user('testadmin');
    $admin->set_password('testtest');
    $admin->name = "Test Admin";
    $admin->set_email($admin_email);
    $admin->container_guid = $root_scope->guid;
    $admin->save();
    
    $set_test_defaults = function($org) use ($root_scope)
    {
        $org->set_design_setting('tagline', 'a test organization');
        $org->set_email(Config::get('admin_email'));    
        $org->set_password('testtest');
        $org->language = 'en';
        $org->set_sectors(array(6,19));
        $org->country = 'tz';
        $org->set_lat_long(-6.140555,35.551758);
        $org->approval = 1;
        $org->container_guid = $root_scope->guid;
    };
    
    for ($i = 0; $i < 22; $i++)
    {
        $org = get_or_create_org("testposter$i");        
        $set_test_defaults($org);        
        $org->set_phone_number("cell: $i$i$i$i$i$i$i, fax: +124124129481");
        $org->name = "Test Poster$i";
        $org->set_email(str_replace('@',"+p$i@", $admin_email));
        $org->save();        
    }
    
    $org = get_or_create_org('testorg');
    $set_test_defaults($org);        
    $org->name = "Test Org";
    $org->save();   
    
    $org = get_or_create_org('testunapproved');
    $set_test_defaults($org);    
    $org->approval = 0;
    $org->name = "Unapproved Org";
    $org->save();       
};