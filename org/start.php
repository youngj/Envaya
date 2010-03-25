<?php

function org_page_handler($page)
{
    global $CONFIG;

    if (isset($page[0]))
    {
        switch($page[0])
        {
            case "new":
                include(dirname(__FILE__) . "/neworg.php");
                return;
            case "help":
                include(dirname(__FILE__) . "/help.php");
                return;                
            case "browse":
                set_page_owner(0);
                include(dirname(__FILE__) . "/browseorgs.php");
                return;
            case "search":
                include(dirname(__FILE__) . "/search.php");
                return;
            case "searchArea":
                include(dirname(__FILE__) . "/searchArea.php");
                return;
            case "feed":    
                include(dirname(__FILE__) . "/feed.php");
                return;
            case "translate":
                set_input("guid", $page[1]);
                set_input("property", $page[2]);
                include(dirname(__FILE__) . "/translate.php");
                return;
            default:
                not_found();
        }
    }
}

function page_page_handler($page)
{
    $pageName = $page[0];
    set_input('page_name', $pageName);
    include(dirname(__FILE__) . "/page.php");
}

function home_page_handler($page)
{
    set_input('page_name','home');
    include(dirname(__FILE__) . "/page.php");
}


function org_profile_page_handler($page)
{                
    $org = get_user_by_username($page[0]);                
    if (!$org)
    {
        include(dirname(__FILE__) . "/orgprofile.php");        
        return;
    }

    set_input('org_guid', $org->guid);
    set_page_owner($org->guid);
    set_context("org");

    if (isset($page[1]))
    {
        switch ($page[1])
        {
            case "mobilesettings":
                include(dirname(__FILE__) . "/mobileSettings.php");
                return;                
            case "post":
                set_input("blogpost", $page[2]);

                switch (@$page[3])
                {
                    case "edit":
                        include(dirname(__FILE__) . "/editPost.php");
                        return;
                    case "preview":
                        include(dirname(__FILE__) . "/postPreview.php");
                        return;                        
                    case "next":
                        set_input("delta", 1);
                        include(dirname(__FILE__) . "/postRedirect.php");
                        return;
                    case "prev":    
                        set_input("delta", -1);
                        include(dirname(__FILE__) . "/postRedirect.php");
                        return;
                    default:
                        include(dirname(__FILE__) . "/blogPost.php");
                        return;
                }
            case "icon":
                set_input('size', $page[2]);
                include(dirname(__FILE__) . "/icon.php");
                return;                
            default:
                break;
        }
        
        if ($page[1])
        {
            $widget = $org->getWidgetByName($page[1]);
            if (isset($page[2]))
            {
                switch ($page[2])
                {
                    case 'edit':
                        set_context("editor");
                        include(dirname(__FILE__) . "/editwidget.php");                    
                        return;
                }       
            }
            
            if ($widget->guid)
            {            
                include(dirname(__FILE__) . "/orgprofile.php");
                return;
            }
            else
            {
                not_found();
                return;
            }
        }
    }    
    
    $widget = null;
    include(dirname(__FILE__) . "/orgprofile.php");
}

function login_page_handler($page)
{
    include(dirname(__FILE__) . "/login.php");
}

function envaya_pagesetup()
{
    if (get_context() == "org")
    {
        $org = page_owner_entity();        

        if (!empty($org))
        {
            $widgets = $org->getAvailableWidgets();
            
            add_submenu_item(elgg_echo("org:home"), $org->getURL());
            
            foreach ($widgets as $widget)
            {
                if ($widget->isActive() && $widget->widget_name != 'home')
                {
                    add_submenu_item(elgg_echo("widget:{$widget->widget_name}"), $widget->getURL());
                }                    
            }     
        }
    }
}

function new_index() 
{
    include(dirname(__FILE__) . "/splash.php");
    return true;
}

function org_settings_save() 
{
    global $CONFIG;
    @include($CONFIG->path . "actions/org/saveSettings.php");
}

function notify_new_org($event, $objectType, $org)
{    
    if (!$org->isApproved())
    {
        send_admin_mail("New organization registered: {$org->name}", 
"To view their website and approve or reject it, visit
{$org->getURL()}?login=1
");
    }
}

register_page_handler('orgprofile','org_profile_page_handler');
register_page_handler('org','org_page_handler');
register_page_handler('page','page_page_handler');
register_page_handler('home','home_page_handler');
register_page_handler('login','login_page_handler');
register_elgg_event_handler('pagesetup','system','envaya_pagesetup');
register_elgg_event_handler('register', 'organization', 'notify_new_org');
register_plugin_hook('index','system','new_index');

extend_elgg_settings_page('org/settings', 'usersettings/user', 1);
register_plugin_hook('usersettings:save','user','org_settings_save');

global $CONFIG;

register_action("org/register1",true,  "{$CONFIG->path}actions/org/register1.php");
register_action("org/register2",true,  "{$CONFIG->path}actions/org/register2.php");
register_action("org/register3",false,  "{$CONFIG->path}actions/org/register3.php");
register_action("org/saveWidget",false, "{$CONFIG->path}actions/org/saveWidget.php");
register_action("org/approve",false,    "{$CONFIG->path}actions/org/approveOrg.php");
register_action("org/verify",false,     "{$CONFIG->path}actions/org/verifyOrg.php");
register_action("org/changeEmail",false,"{$CONFIG->path}actions/org/changeEmail.php");
register_action("org/requestPartner",false,"{$CONFIG->path}actions/org/requestPartner.php");
register_action("org/createPartner",false,"{$CONFIG->path}actions/org/createPartner.php");
register_action("changeLanguage", true, "{$CONFIG->path}actions/org/changeLanguage.php");
register_action("translate", false,     "{$CONFIG->path}actions/org/translate.php");
register_action("news/add",false,       "{$CONFIG->path}actions/org/addPost.php");
register_action("news/edit",false,      "{$CONFIG->path}actions/org/editPost.php");
register_action("news/delete",false,    "{$CONFIG->path}actions/org/deletePost.php");
register_action("entities/delete",false,"{$CONFIG->path}actions/entities/delete.php");


?>