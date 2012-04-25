<?php

class Module_Envaya_Org extends Module
{
    static $autoload_patch = array(
        'ClassRegistry',
        'PrefixRegistry',
        'Controller_Default',
        'Controller_Admin',
        'Language',
        'Hook_ViewWidget',
        'Hook_ViewDashboard',
    );

    static $view_patch = array(
        'account/login_links',
        'account/register_content',
        'page_elements/body_start',
        'page_elements/head_content',
        'css/default',
        'css/editor',
        'messages/usersite',
        'messages/dashboard',
        'emails/network_relationship_invite_link'
    );

    static function patch_view_account_login_links(&$views)
    {
        $views[] = 'account/envaya_login_links';
    }

    static function patch_view_account_register_content(&$views)
    {
        array_unshift($views, 'account/envaya_register_content');
    }

    static function patch_view_page_elements_body_start(&$views)
    {
        $views[] = 'page_elements/envaya_topbar';
    }

    static function patch_view_page_elements_head_content(&$views)
    {
        $views[] = 'page_elements/envaya_favicon';
    }

    static function patch_view_css_default(&$views)
    {
        $views[] = 'css/snippets/topbar';
    }

    static function patch_view_css_editor(&$views)
    {
        $views[] = 'css/snippets/slideshow';
    }

    static function patch_view_messages_usersite(&$views)
    {
        array_unshift($views, 'messages/envaya_usersite');
    }

    static function patch_view_messages_dashboard(&$views)
    {
        array_unshift($views, 'messages/envaya_dashboard');
    }

    static function patch_view_emails_network_relationship_invite_link(&$views)
    {
        $views = array('emails/envaya_relationship_invite');
    }

    static function patch_ClassRegistry()
    {
        ClassRegistry::register(array(
            'core.featured.site' => 'FeaturedSite',
            'core.featured.photo' => 'FeaturedPhoto',
            'core.permission.editmainsite' => 'Permission_EditMainSite',
        ));
    }
    
    static function patch_PrefixRegistry()
    {
        PrefixRegistry::register(array(
            'eS' => 'FeaturedSite',
            'eP' => 'FeaturedPhoto'
        ));
    }    

    static function patch_Controller_Default()
    {
        array_unshift(Controller_Default::$routes, 
            array(
                'regex' => '/org\b',
                'controller' => 'Controller_Org',
            ), 
            array(
                'regex' => '/($|home\b)',
                'controller' => 'Controller_EnvayaHome',
            )
        );
    }

    static function patch_Controller_Admin()
    {
        Controller_Admin::$routes[] = array(
            'regex' => '/envaya\b',
            'controller' => 'Controller_EnvayaAdmin',
        );
    }

    static function patch_Language()
    {
        Language::add_fallback_group('featured', 'featured_admin');
    }

    static function patch_Hook_ViewWidget()
    {
        Hook_ViewWidget::register_handler('Handler_EnvayaViewWidget');
    }

    static function patch_Hook_ViewDashboard()
    {
        Hook_ViewDashboard::register_handler('Handler_EnvayaViewDashboard');
    }
}
