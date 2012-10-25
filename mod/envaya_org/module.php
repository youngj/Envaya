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
        'Theme_UserSite',
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
            
            'envaya.theme.leftmenu' => 'Theme_LeftMenu',
            'envaya.theme.leftmenu.dark' => 'Theme_LeftMenuDark',
            'envaya.theme.leftmenu.blue' => 'Theme_LeftMenuBlue',
            'envaya.theme.leftmenu.green' => 'Theme_LeftMenuGreen',
            'envaya.theme.leftmenu.yellow' => 'Theme_LeftMenuYellow',
            'envaya.theme.leftmenu.purple' => 'Theme_LeftMenuPurple',
            'envaya.theme.leftmenu.lightblue' => 'Theme_LeftMenuLightBlue',
            'envaya.theme.leftmenu.red' => 'Theme_LeftMenuRed',
            'envaya.theme.leftmenu.brown' => 'Theme_LeftMenuBrown',
            'envaya.theme.leftmenu.sage' => 'Theme_LeftMenuSage',
            'envaya.theme.leftmenu.bluegray' => 'Theme_LeftMenuBlueGray',
            'envaya.theme.leftmenu.lavender' => 'Theme_LeftMenuLavender',
            'envaya.theme.leftmenu.moss' => 'Theme_LeftMenuMoss',
            'envaya.theme.leftmenu.white' => 'Theme_LeftMenuWhite',
            'envaya.theme.leftmenu.pink' => 'Theme_LeftMenuPink',
            
            'envaya.theme.lightgray' => 'Theme_LightGray',
            'envaya.theme.lightblue' => 'Theme_LightBlue',
            'envaya.theme.lightblue2' => 'Theme_LightBlue2',
            'envaya.theme.sage' => 'Theme_Sage',
            'envaya.theme.pink' => 'Theme_Pink',
            'envaya.theme.bluegray' => 'Theme_BlueGray',
            'envaya.theme.lavender' => 'Theme_Lavender',
            'envaya.theme.moss' => 'Theme_Moss',
            'envaya.theme.white' => 'Theme_White',
            'envaya.theme.blue' => 'Theme_Blue',
            'envaya.theme.red' => 'Theme_Red',
            'envaya.theme.yellow' => 'Theme_Yellow',
            'envaya.theme.purple' => 'Theme_Purple',
            'envaya.theme.dark' => 'Theme_Dark',
            'envaya.theme.green' => 'Theme_Green',
            'envaya.theme.brown' => 'Theme_Brown',
            'envaya.theme.personprofile' => 'Theme_PersonProfile',
            'envaya.theme.beads' => 'Theme_Beads',                        
            'envaya.theme.chrome' => 'Theme_Chrome',
            'envaya.theme.brick' => 'Theme_Brick',
            'envaya.theme.cotton' => 'Theme_Cotton',
            'envaya.theme.craft1' => 'Theme_Craft1',
            'envaya.theme.craft4' => 'Theme_Craft4',
            'envaya.theme.wovengrass' => 'Theme_WovenGrass',
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
    
    static function patch_Theme_UserSite()
    {
        Theme_UserSite::add_available_themes(array(
            'Theme_LeftMenu',
            'Theme_LeftMenuLightBlue',
            'Theme_LeftMenuBrown',
            'Theme_LeftMenuSage',
            'Theme_LeftMenuBlueGray',
            'Theme_LeftMenuLavender',
            'Theme_LeftMenuMoss',
            'Theme_LeftMenuPink',
            'Theme_LeftMenuYellow',                                    
            'Theme_LeftMenuWhite',
            'Theme_LeftMenuGreen',
            'Theme_LeftMenuRed',
            'Theme_LeftMenuBlue',            
            'Theme_LeftMenuPurple',
            'Theme_LeftMenuDark', 

            'Theme_LightBlue',            
            'Theme_Beads',
            'Theme_Brick',
            'Theme_Cotton',
            'Theme_Craft1',
            'Theme_Craft4',
            'Theme_WovenGrass',           
            'Theme_Chrome',
            
            'Theme_LightGray',
            'Theme_LightBlue2',
            'Theme_Brown',
            'Theme_Sage',
            'Theme_BlueGray',
            'Theme_Lavender',
            'Theme_Moss',
            'Theme_Pink',
            'Theme_Yellow',            
            'Theme_White',            
            'Theme_Green',
            'Theme_Red',
            'Theme_Blue',
            'Theme_Purple',
            'Theme_Dark',
        ));

        Theme_UserSite::add_patterns(array(           
            'background:gradient' => '#fff url(/_media/images/section_content.gif) repeat-x left -15px',
            'background:gradient2' => '#fff url(/_media/images/section_content.gif) repeat-x left top',
            'background:gradient3' => '#e5e5e5 url(/_media/images/lightblue/thin_column.gif) repeat-x left top',
            'background:beads' => '#f2c346 url(/_media/images/beads/beads.jpg) repeat -100px -60px',
            'background:wood' => '#25160d url(/_media/images/beads/wood_header.jpg) repeat left bottom',
            'background:brick' => '#69493e url(/_media/images/brick/brick.jpg) repeat left top',
            'background:cotton' => '#d0b66b url(/_media/images/cotton/cotton-bg.jpg) repeat left top',
            'background:craft1' => '#f2c346 url(/_media/images/craft/craft1-bg.jpg) repeat left -60px',
            'background:craft4' => '#f2c346 url(/_media/images/craft/craft4-bg.jpg) repeat left -60px',
            'background:wood2' => '#461600 url(/_media/images/craft/craft4-header.jpg) repeat -80px -20px',
            'background:wovengrass' => '#d5b24a url("/_media/images/wovengrass/woven-grass.jpg") repeat left -60px',
            'background:light_wovengrass' => '#f0e3a7 url("_media/images/wovengrass/woven-grass-2-textbg.jpg") repeat -30px -60px',
            'background:light_pink_gradient' => '#fff url("/_media/images/beads/section_content.gif") repeat-x left top',
            'background:light_gray_gradient' => '#fff url("/_media/images/simple/bg_gradient.gif") repeat-x left 62px',
            'background:beige_gradient' => '#f4eebd url("/_media/images/craft/section_content.gif") repeat-x left top',
            'background:yellow_gradient' => '#fdffe9 url("/_media/images/wovengrass/section_content.gif") repeat-x left top',
            'menu_button:blue' => "#d5d0c8 url(/_media/images/lightblue/button.png)",                        
            'section_header:blue' => '#e6e6e6 url(/_media/images/section_header.gif) repeat-x left -5px',            
            'section_header:purple' => '#4e2237 url("/_media/images/beads/section_header.gif") repeat-x left top',
            'section_header:dark_gray' => '#2a2a2a url("/_media/images/brick/section_header.gif") repeat-x left top',
            'section_header:beige' => '#bb895a url("/_media/images/cotton/section_header.gif") repeat-x left top',
            'section_header:brown' => '#0f1f29 url("_media/images/craft/section_header.gif") repeat-x left top',
            'section_header:brown2' => '#ad9e61 url("_media/images/wovengrass/section_header.gif") repeat-x left top',
            'left_menu_background:gray' => 'url(/_media/images/leftmenu/menu_selected3.png) no-repeat 3px top',
            'box_shadow:gray' => '1px 1px 10px #ccc',
            'box_shadow:black' => '1px 1px 10px #000',
        ));
    }
}
