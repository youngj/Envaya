<?php
    
return function() {

    $envaya = get_or_create_org('envaya');

    $envaya->email = Config::get('admin_email');
    $envaya->name = 'Envaya';
    $envaya->set_password('testtest');
    $envaya->language = 'en';
    $envaya->set_design_setting('theme_name', 'sidebar');
    $envaya->country = 'us';
    $envaya->set_lat_long(37,-112);
    $envaya->approval = 1;
    $envaya->save();
    
    $home = $envaya->get_widget_by_name('home');
    $home->subclass = 'Hardcoded';
    $home->handler_arg = 'page/about';
    $home->title = 'About Us';
    $home->save();
      
    $envaya->get_widget_by_name('news')->save();

    $contact = $envaya->get_widget_by_name('contact');
    $contact->subclass = 'Hardcoded';
    $contact->handler_arg = 'page/contact';
    $contact->save();

    $donate = $envaya->get_widget_by_name('contribute');
    $donate->subclass = 'Hardcoded';
    $donate->handler_arg = 'page/donate';
    $donate->title = 'Contribute';
    $donate->in_menu = 1;
    $donate->save();

};