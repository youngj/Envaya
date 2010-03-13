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
            case "checkmail":
                include(dirname(__FILE__) . "/checkmail.php");
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
            case "translate":
                set_input("guid", $page[1]);
                set_input("property", $page[2]);
                include(dirname(__FILE__) . "/translate.php");
                return;
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

    set_input('org_guid', $org->guid);
    set_page_owner($org->guid);
    set_context("org");

    if (isset($page[1]))
    {
        switch ($page[1])
        {
            case "news":
                set_context("blog");
                include(dirname(__FILE__) . "/blog.php");
                return;
            case "newpost";
                include(dirname(__FILE__) . "/newPost.php");
                return;
            case "mobilesettings":
                include(dirname(__FILE__) . "/mobileSettings.php");
                return;
            case "editmap":
                include(dirname(__FILE__) . "/editMap.php");
                return;
            case "post":
                set_context("blog");
                set_input("blogpost", $page[2]);

                switch ($page[3])
                {
                    case "edit":
                        include(dirname(__FILE__) . "/editPost.php");
                        return;
                    case "image":
                        set_input("size", $page[4]);
                        include(dirname(__FILE__) . "/postImage.php");
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
            case "edit":
                include(dirname(__FILE__) . "/editOrg.php");
                return;
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
            if ($page[2] == 'edit')
            {
                set_context("widget");
                include(dirname(__FILE__) . "/editwidget.php");                    
                return;
            }
            else if ($widget->guid)
            {            
                include(dirname(__FILE__) . "/orgprofile.php");
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
    if (get_context() == "blog" || get_context() == "org")
    {
        $org = page_owner_entity();        

        if (!empty($org))
        {
            $widgets = $org->getActiveWidgets();
            foreach ($widgets as $widget)
            {
                if ($widget->widget_name != 'home')
                {
                    add_submenu_item(elgg_echo("widget:{$widget->widget_name}"), $widget->getURL());
                }    
            }     
            
            /*
            if (can_write_to_container(0, $org->guid))
            {                        
                //add_submenu_item(elgg_echo('org:mobilesettings'),$org->getUrl()."/mobilesettings");                
                add_submenu_item(elgg_echo('blog:addpost'),$org->getUrl()."/newpost");
            }    
            */
        }
    }
}

function new_index() 
{
    include(dirname(__FILE__) . "/splash.php");
    return true;
}

register_page_handler('orgprofile','org_profile_page_handler');
register_page_handler('org','org_page_handler');
register_page_handler('page','page_page_handler');
register_page_handler('home','home_page_handler');
register_page_handler('login','login_page_handler');
register_elgg_event_handler('pagesetup','system','envaya_pagesetup');
register_plugin_hook('index','system','new_index');

global $CONFIG;

register_action("org/register1",false,  "{$CONFIG->path}actions/org/register1.php");
register_action("org/register2",false,  "{$CONFIG->path}actions/org/register2.php");
register_action("org/register3",false,  "{$CONFIG->path}actions/org/register3.php");
register_action("org/saveWidget",false, "{$CONFIG->path}actions/org/saveWidget.php");
register_action("org/edit",false,       "{$CONFIG->path}actions/org/editOrg.php");
register_action("org/delete",false,     "{$CONFIG->path}actions/org/deleteOrg.php");
register_action("org/approve",false,    "{$CONFIG->path}actions/org/approveOrg.php");
register_action("org/verify",false,     "{$CONFIG->path}actions/org/verifyOrg.php");
register_action("org/changeEmail",false,"{$CONFIG->path}actions/org/changeEmail.php");
register_action("org/editMap",false,    "{$CONFIG->path}actions/org/editMap.php");
register_action("changeLanguage", true, "{$CONFIG->path}actions/org/changeLanguage.php");
register_action("translate", false,     "{$CONFIG->path}actions/org/translate.php");
register_action("news/add",false,       "{$CONFIG->path}actions/org/addPost.php");
register_action("news/edit",false,      "{$CONFIG->path}actions/org/editPost.php");
register_action("news/delete",false,    "{$CONFIG->path}actions/org/deletePost.php");
register_action("entities/delete",false,"{$CONFIG->path}actions/entities/delete.php");


?>