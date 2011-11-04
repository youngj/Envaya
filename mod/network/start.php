<?php

Engine::add_autoload_action('ClassRegistry', function() {
    ClassRegistry::register(array(
        'core.user.org.relation' => 'Relationship',        
        'core.subscription.email.network' => "EmailSubscription_Network",
        'core.feeditem.relation' => 'FeedItem_Relationship',
        'core.widget.network' => 'Widget_Network',
    ));
});

Engine::add_autoload_action('Controller_UserSite', function() {
    Controller_UserSite::add_route(array(
        'regex' => '/network/x\b', 
        'controller' => 'Controller_Network', 
    ),0);
});        

Engine::add_autoload_action('Widget', function() {
    Widget::add_default_class('Widget_Network');
});

Engine::add_autoload_action('EmailSubscription', function() {
    EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Network';
});

Views::extend('account/share_shortcuts', 'account/network_share_shortcuts');
