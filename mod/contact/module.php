<?php

class Module_Contact extends Module
{
    static $autoload_patch = array(
        'ClassRegistry',
        'Controller_Admin',
        'Hook_ViewDashboard',
        'Hook_ViewWidget',
        'Language',
        'EmailSubscription',
        'SMSSubscription',
    );

    static function patch_ClassRegistry()
    {
        ClassRegistry::register(array(
            'contact.email.template' => 'EmailTemplate',
            'contact.subscription.email.contact' => "EmailSubscription_Contact",
            'contact.subscription.sms.contact' => "SMSSubscription_Contact",
            'contact.sms.template' => 'SMSTemplate'
        ));
    }

    static function patch_Controller_Admin()
    {
        Controller_Admin::$routes[] = array(
            'regex' => '/contact\b',
            'controller' => 'Controller_Contact',
        );
    }

    static function patch_Hook_ViewDashboard()
    {
        Hook_ViewDashboard::register_handler('Handler_ContactViewDashboard', -1000);
    }

    static function patch_Hook_ViewWidget()
    {
        Hook_ViewWidget::register_handler('Handler_ContactViewWidget');
    }

    static function patch_Language()
    {
        Language::add_fallback_group('contact', 'contact_admin');
    }

    static function patch_EmailSubscription()
    {
        EmailSubscription::$self_subscription_classes[] = 'EmailSubscription_Contact';
    }

    static function patch_SMSSubscription()
    {
        SMSSubscription::$self_subscription_classes[] = 'SMSSubscription_Contact';
    }
}
