<?php

require_once("engine/start.php");

function main()
{
    //install_admin();
    install_testorg();
    install_envaya();
}

function install_admin()
{
    global $CONFIG;
    $admin = register_user("testadmin", 'testtest', "Test Admin", $CONFIG->admin_email, true);
    $admin->admin = true;    
    $admin->save();
}

function install_testorg()
{
    global $CONFIG;
    $org = Organization::query()->where('username = ?', 'testorg')->get();
    if (!$org)
    {    
        $org = new Organization();
        $org->username = 'testorg';
        $org->email = $CONFIG->admin_email;
        $org->name = "Test Org";
        $org->set_password('testtest');
        $org->language = 'en';
        $org->country = 'tz';
        $org->setup_state = SetupState::CreatedHomePage;
        $org->set_lat_long(-6.140555,35.551758);
        $org->approval = 1;
        $org->save();
    }

    $org->get_widget_by_name('home')->save();
    $org->get_widget_by_name('news')->save();
    $org->get_widget_by_name('contact')->save();
}
    
function install_envaya()    
{
    global $CONFIG;
    $envaya = Organization::query()->where('username = ?', 'envaya')->get();
    if (!$envaya)
    {
        $envaya = new Organization();
        $envaya->username = 'envaya';
        $envaya->email = $CONFIG->admin_email;
        $envaya->name = 'Envaya';
        $envaya->set_password('testtest');
        $envaya->language = 'en';
        $envaya->theme = 'sidebar';
        $envaya->country = 'us';
        $envaya->set_lat_long(37,-112);
        $envaya->setup_state = SetupState::CreatedHomePage;
        $envaya->approval = 1;
        $envaya->save();
    }
    
    $home = $envaya->get_widget_by_name('home');
    $home->handler_class = 'WidgetHandler_Hardcoded';
    $home->handler_arg = 'page/about';
    $home->title = 'About Us';
    $home->save();
      
    $envaya->get_widget_by_name('news')->save();

    $contact = $envaya->get_widget_by_name('contact');
    $contact->handler_class = 'WidgetHandler_Hardcoded';
    $contact->handler_arg = 'page/contact';
    $contact->save();

    $donate = $envaya->get_widget_by_name('contribute');
    $donate->handler_class = 'WidgetHandler_Hardcoded';
    $donate->handler_arg = 'page/donate';
    $donate->title = 'Contribute';
    $donate->in_menu = 1;
    $donate->save();
}

main();
    
$CONFIG->debug = false;
print "done";