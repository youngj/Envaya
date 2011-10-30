<?php

Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtypes(array(
        'contact.email.template' => 'EmailTemplate',
        'contact.subscription.email.contact' => "EmailSubscription_Contact",
        'contact.subscription.sms.contact' => "SMSSubscription_Contact",
        'contact.sms.template' => 'SMSTemplate'
    ));
});

Engine::add_autoload_action('Controller_Admin', function() {
    Controller_Admin::add_route(array(
        'regex' => '/contact\b',
        'controller' => 'Controller_Contact',
    ));           
});

Views::extend('account/links_items', 'account/contact_links_items', -1000);    
Views::extend('admin/user_actions_items', 'admin/contact_user_actions_items');    

Config::load_module_defaults('contact');

Engine::add_autoload_action('Language', function() {
    Language::add_fallback_group('contact', 'contact_admin');
});

Engine::add_autoload_action('EmailSubscription', function() {
    EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Contact';
});

Engine::add_autoload_action('SMSSubscription', function() {
    SMSSubscription::$self_subscription_classes[] = 'SMSSubscription_Contact';
});
