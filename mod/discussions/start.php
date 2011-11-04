<?php
        
Engine::add_autoload_action('ClassRegistry', function() {
    ClassRegistry::register(array(    
        'core.discussion.message' => 'DiscussionMessage',
        'core.discussion.topic' => 'DiscussionTopic',
        'core.subscription.email.discussion' => "EmailSubscription_Discussion",       
        'core.permission.editdiscussionmessage' => 'Permission_EditDiscussionMessage',
        'core.feeditem.discussion.message' => 'FeedItem_Message',
        'core.widget.discussions' => 'Widget_Discussions',
    ));
});

Engine::add_autoload_action('Widget', function() {
    Widget::add_default_class('Widget_Discussions');
});
        
Engine::add_autoload_action('Controller_UserSite', function() {
    Controller_UserSite::add_route(array(
        'regex' => '/topic\b', 
        'controller' => 'Controller_DiscussionTopic', 
    ),0);
});        

Engine::add_autoload_action('Controller_Pg', function() {
    Controller_Pg::add_route(array(
        'regex' => '/discussions\b', 
        'controller' => 'Controller_Pg_Discussions', 
    ));
});

Engine::add_autoload_action('IncomingMail', function() {
    IncomingMail::add_tag_action('#^message(?P<guid>\d+)$#', 
        array('EmailSubscription_Discussion', 'handle_mail_reply'));
});

Engine::add_autoload_action('EmailSubscription', function() {
    EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Discussion';
});
