<?php
    
return function() {

    $testadmin = User::get_by_username('testadmin');
    $root = UserScope::get_root();
    Permission_EditMainSite::grant_explicit($root, $testadmin);

    $envaya = get_or_create_org('envaya');

    $envaya->email = Config::get('mail:admin_email');
    $envaya->name = 'Envaya';
    $envaya->set_password('asdfasdf');
    $envaya->language = 'en';
    $envaya->set_design_setting('theme_name', 'sidebar');
    $envaya->country = 'us';
    $envaya->set_lat_long(37,-112);
    $envaya->approval = 1;
    $envaya->save();
    
    $home = Widget_Home::get_for_entity($envaya);
    if ($home)
    {
        $home->delete();
    }
    
    $home = $envaya->get_widget_by_name('home') ?: Widget_Hardcoded::new_for_entity($envaya);
    $home->menu_order = 0;
    $home->widget_name = 'home';
    $home->handler_arg = 'page/about';
    $home->title = 'About Us';
    $home->save();
      
    Widget_News::get_or_init_for_entity($envaya);
    
    $contact = Widget_Contact::get_for_entity($envaya);
    if ($contact)
    {
        $contact->delete();
    }

    $contact = $envaya->get_widget_by_name('contact') ?: Widget_Hardcoded::new_for_entity($envaya);
    $contact->widget_name = 'contact';
    $contact->title = 'Contact';
    $contact->handler_arg = 'page/contact';
    $contact->save();

    $donate = $envaya->get_widget_by_name('contribute') ?: Widget_Hardcoded::new_for_entity($envaya);
    $donate->widget_name = 'contribute';
    $donate->handler_arg = 'page/donate';
    $donate->title = 'Contribute';
    $donate->in_menu = 1;
    $donate->save();

};