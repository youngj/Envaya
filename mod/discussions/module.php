<?php

class Module_Discussions extends Module
{        
    static $autoload_patch = array(
        'ClassRegistry',
        'Widget',
        'Controller_UserSite',   
        'Controller_Pg',
        'IncomingMail',     
        'EmailSubscription',
    );

    static function patch_ClassRegistry()
    {
        ClassRegistry::register(array(    
            'core.discussion.message' => 'DiscussionMessage',
            'core.discussion.topic' => 'DiscussionTopic',
            'core.subscription.email.discussion' => "EmailSubscription_Discussion",       
            'core.permission.editdiscussionmessage' => 'Permission_EditDiscussionMessage',
            'core.feeditem.discussion.message' => 'FeedItem_Message',
            'core.widget.discussions' => 'Widget_Discussions',
        ));
    }

    static function patch_Widget()
    {
        Widget::add_default_class('Widget_Discussions');
    }
        
    static function patch_Controller_UserSite()
    {
        Controller_UserSite::add_route(array(
            'regex' => '/topic\b', 
            'controller' => 'Controller_DiscussionTopic', 
        ),0);
    }      

    static function patch_Controller_Pg()
    {
        Controller_Pg::add_route(array(
            'regex' => '/discussions\b', 
            'controller' => 'Controller_Pg_Discussions', 
        ));
    }

    static function patch_IncomingMail()
    {
        IncomingMail::add_tag_action('#^message(?P<guid>\d+)$#', 
            array('EmailSubscription_Discussion', 'handle_mail_reply'));
    }

    static function patch_EmailSubscription()
    {
        EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Discussion';
    }
}

