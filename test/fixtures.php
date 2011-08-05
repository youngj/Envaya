<?php

return function() {
    $admin_email = Config::get('admin_email');
    
    $admin = get_or_create_user('testadmin');
    $admin->set_password('testtest');
    $admin->name = "Test Admin";
    $admin->email = $admin_email;
    $admin->admin = true;    
    $admin->save();    
    
    $set_test_defaults = function($org)
    {
        $org->set_design_setting('tagline', 'a test organization');
        $org->email = Config::get('admin_email');    
        $org->set_password('testtest');
        $org->language = 'en';
        $org->set_sectors(array(6,19));
        $org->country = 'tz';
        $org->set_lat_long(-6.140555,35.551758);
        $org->approval = 1;
    };
    
    for ($i = 0; $i < 22; $i++)
    {
        $org = get_or_create_org("testposter$i");        
        $set_test_defaults($org);        
        $org->phone_number = "cell: $i$i$i$i$i$i$i, fax: +124124129481";                        
        $org->name = "Test Poster$i";
        $org->email = str_replace('@',"+p$i@", $admin_email);
        $org->save();        
    }
    
    $org = get_or_create_org('testorg');
    $set_test_defaults($org);        
    $org->name = "Test Org";
    $org->save();   
};