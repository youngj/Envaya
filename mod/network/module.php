<?php

class Module_Network extends Module
{
    static $autoload_patch = array(
        'ClassRegistry',
        'Controller_UserSite',
        'Widget',
        'EmailSubscription',
    );

    static $view_patch = array(
        'account/share_shortcuts',
    );

    static function patch_view_account_share_shortcuts(&$views)
    {
        $views[] = 'account/network_share_shortcuts';
    }

    static function patch_ClassRegistry()
    {
        ClassRegistry::register(array(
            'core.user.org.relation' => 'Relationship',        
            'core.subscription.email.network' => "EmailSubscription_Network",
            'core.feeditem.relation' => 'FeedItem_Relationship',
            'core.widget.network' => 'Widget_Network',
        ));
    }

    static function patch_Controller_UserSite()
    {
        Controller_UserSite::add_route(array(
            'regex' => '/network/x\b', 
            'controller' => 'Controller_Network', 
        ),0);
    }

    static function patch_Widget()
    {
        Widget::add_default_class('Widget_Network');
    }

    static function patch_EmailSubscription()
    {
        EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Network';
    }
}
