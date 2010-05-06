<?php

function org_page_handler($page)
{
    global $CONFIG;

    add_generic_footer();

    if (isset($page[0]))
    {
        switch($page[0])
        {
            case "new":
                include(__DIR__ . "/neworg.php");
                return;
            case "help":
                include(__DIR__ . "/help.php");
                return;
            case "browse":
                set_page_owner(0);
                include(__DIR__ . "/browseorgs.php");
                return;
            case "search":
                include(__DIR__ . "/search.php");
                return;
            case "searchArea":
                include(__DIR__ . "/searchArea.php");
                return;
            case "feed":
                include(__DIR__ . "/feed.php");
                return;
            case "translate":
                include(__DIR__ . "/translate.php");
                return;
            case "translateQueue":
                include(__DIR__ . "/translateQueue.php");
                return;
            default:
                not_found();
        }
    }
}

function page_page_handler($page)
{
    $pageName = $page[0];
    if ($pageName == 'home')
    {
        home_page_handler($page);
    }
    else
    {
        add_generic_footer();
        set_input('page_name', $pageName);
        include(__DIR__ . "/page.php");
    }
}

function home_page_handler($page)
{
    add_generic_footer();
    include(__DIR__ . "/home.php");
}


function org_profile_page_handler($page)
{
    $org = get_user_by_username($page[0]);
    if (!$org)
    {
        include(__DIR__ . "/orgprofile.php");
        return;
    }

    set_input('org_guid', $org->guid);
    set_page_owner($org->guid);
    set_context('orgprofile');

    if (isset($page[1]))
    {
        switch ($page[1])
        {
            case "design":
                include(__DIR__ . "/design.php");
                return;
            case "username":
                include(__DIR__ . "/changeUsername.php");
                return;
            case "confirm":
                include(__DIR__ . "/confirmPartner.php");
                return;
            case "compose":
                include(__DIR__ . "/composeMessage.php");
                return;
            case "addphotos":
                include(__DIR__ . "/addPhotos.php");
                return;
            case "teammember":
                set_input("member_guid", @$page[2]);
                switch (@$page[3])
                {
                    case "edit":
                        include(__DIR__."/editTeamMember.php");
                        return;
                    default:
                        break;
                }
                break;
            case "post":
                set_input("blogpost", $page[2]);

                switch (@$page[3])
                {
                    case "edit":
                        include(__DIR__ . "/editPost.php");
                        return;
                    case "preview":
                        include(__DIR__ . "/postPreview.php");
                        return;
                    case "next":
                        set_input("delta", 1);
                        include(__DIR__ . "/postRedirect.php");
                        return;
                    case "prev":
                        set_input("delta", -1);
                        include(__DIR__ . "/postRedirect.php");
                        return;
                    default:
                        include(__DIR__ . "/blogPost.php");
                        return;
                }
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
                        include(__DIR__ . "/editwidget.php");
                        return;
                }
            }

            if ($widget->guid)
            {
                include(__DIR__ . "/orgprofile.php");
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
    include(__DIR__ . "/orgprofile.php");
}

function login_page_handler($page)
{
    include(__DIR__ . "/login.php");
}

function envaya_pagesetup()
{
    if (get_context() == 'orgprofile')
    {
        $org = page_owner_entity();

        if (!empty($org))
        {
            $widgets = $org->getAvailableWidgets();

            add_submenu_item(elgg_echo("widget:home"), $org->getURL());

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
    include(__DIR__ . "/splash.php");
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
    	post_feed_items($org, 'register', $org);

        send_admin_mail("New organization registered: {$org->name}",
"To view their website and approve or reject it, visit
{$org->getURL()}?login=1
");
    }
}

function add_generic_footer()
{
    add_submenu_item(elgg_echo('about:link'), "/page/about", 'footer');
    add_submenu_item(elgg_echo('contact:link'), "/page/contact", 'footer');
    add_submenu_item(elgg_echo('donate:link'), "/page/donate", 'footer');
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


