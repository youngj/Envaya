<?php

Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtype('core.email.template', 'EmailTemplate');
});

Engine::add_autoload_action('Controller_Admin', function() {
    Controller_Admin::add_route(array(
        'regex' => '/contact\b',
        'defaults' => array('controller' => 'Contact')
    ), 0);    
});

Views::extend('admin/dashboard_items', 'admin/contact_dashboard_items', -1000);

Config::load_module_defaults('contact');
