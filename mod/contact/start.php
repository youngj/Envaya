<?php

Engine::add_autoload_action('ClassRegistry', function() {
    ClassRegistry::register(array(
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

Engine::add_autoload_action('Hook_ViewDashboard', function() {    
    Hook_ViewDashboard::register_handler('Handler_ContactViewDashboard', -1000);
});

Engine::add_autoload_action('Hook_ViewWidget', function() {    
    Hook_ViewWidget::register_handler('Handler_ContactViewWidget');
});

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
