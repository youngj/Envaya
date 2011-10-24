<?php
        
Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtype('core.discussion.message', 'DiscussionMessage');
    EntityRegistry::register_subtype('core.discussion.topic', 'DiscussionTopic');
});

Engine::add_autoload_action('Widget', function() {
    Widget::add_default_widget('discussions', array(
        'menu_order' => 70, 
        'page' => true, 
        'subclass' => 'Discussions'
    ));
});

Engine::add_autoload_action('Controller_Pg', function() {
    Controller_Pg::add_route(array(
        'regex' => '/discussions\b', 
        'controller' => 'Controller_Pg_Discussions', 
    ),0);
});

Engine::add_autoload_action('IncomingMail', function() {
    IncomingMail::add_tag_action('#^message(?P<guid>\d+)$#', 
        array('EmailSubscription_Discussion', 'handle_mail_reply'));
});

Engine::add_autoload_action('EmailSubscription', function() {
    EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Discussion';
    EmailSubscription::$admin_subscription_classes[] = 'EmailSubscription_Discussion';
});
