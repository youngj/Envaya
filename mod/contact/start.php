<?php

Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtype('contact.email.template', 'EmailTemplate');
});

Engine::add_autoload_action('Controller_Admin', function() {
    Controller_Admin::add_route(array(
        'regex' => '/contact\b',
        'controller' => 'Controller_Contact',
    ));           
});

Views::extend('admin/dashboard_items', 'admin/contact_dashboard_items', -1000);    

Config::load_module_defaults('contact');

Engine::add_autoload_action('Language', function() {
    Language::add_fallback_group('contact', 'contact_admin');
});
