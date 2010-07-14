<?php

function org_page_handler($page)
{
    add_generic_footer();

    if (isset($page[0]))
    {
        switch($page[0])
        {
            case "new":             return include(__DIR__."/neworg.php");
            case "help":            return include(__DIR__."/help.php");
            case "browse":          return include(__DIR__."/browseorgs.php");
            case "search":          return include(__DIR__."/search.php");
            case "searchArea":      return include(__DIR__."/searchArea.php");
            case "feed":            return include(__DIR__."/feed.php");
            case "translate":       return include(__DIR__."/translate.php");
            case "translateQueue":  return include(__DIR__."/translateQueue.php");
            case "emailSettings":   return include(__DIR__."/emailSettings.php");
            case "sendEmail":       return include(__DIR__."/sendEmail.php");
            case "contact":         return include(__DIR__."/contact.php");
            case "selectImage":     return include(__DIR__."/selectImage.php");
            case "viewEmail":       return include(__DIR__."/viewEmail.php");
            default:                not_found();
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
        include(__DIR__."/page.php");
    }
}

function home_page_handler($page)
{
    add_generic_footer();
    include(__DIR__."/home.php");
}


function org_profile_page_handler($page)
{
    $org = get_user_by_username($page[0]);
    if (!$org)
    {
        return include(__DIR__."/orgprofile.php");
    }

    set_input('org_guid', $org->guid);
    set_page_owner($org->guid);
    set_context('orgprofile');

    if (isset($page[1]))
    {
        switch ($page[1])
        {
            case "design":      return include(__DIR__."/design.php");
            case "help":        return include(__DIR__."/help.php");
            case "feed":        return include(__DIR__."/orgfeed.php");
            case "related":     return include(__DIR__."/related.php");
            case "dashboard":   return include(__DIR__."/dashboard.php");
            case "username":    return include(__DIR__."/changeUsername.php");
            case "confirm":     return include(__DIR__."/confirmPartner.php");
            case "compose":     return include(__DIR__."/composeMessage.php");
            case "addphotos":   return include(__DIR__."/addPhotos.php");
            case "teammember":
                set_input("member_guid", @$page[2]);
                switch (@$page[3])
                {
                    case "edit": return include(__DIR__."/editTeamMember.php");
                    default: break;
                }
                break;
            case "post":
                set_input("blogpost", $page[2]);

                switch (@$page[3])
                {
                    case "edit":    return include(__DIR__."/editPost.php");
                    case "preview": return include(__DIR__."/postPreview.php");
                    case "next":
                        set_input("delta", 1);
                        return include(__DIR__."/postRedirect.php");
                    case "prev":
                        set_input("delta", -1);
                        return include(__DIR__."/postRedirect.php");
                    default:
                        return include(__DIR__."/blogPost.php");
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
                    case 'edit': return include(__DIR__."/editwidget.php");
                }
            }

            if ($widget->guid)
            {
                return include(__DIR__."/orgprofile.php");
            }
            else
            {
                return org_page_not_found($org);
            }
        }
    }

    $widget = null;
    include(__DIR__."/orgprofile.php");
}

function login_page_handler($page)
{
    include(__DIR__."/login.php");
}

function envaya_pagesetup()
{
    error_log("envaya_pagesetup");

    if (get_input('login'))
    {
        gatekeeper();
    }

    if (get_context() == 'orgprofile')
    {
        error_log("envaya_pagesetup 1");
        $org = page_owner_entity();

        if (!empty($org))
        {
            error_log("envaya_pagesetup 2");
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
    home_page_handler(null);
    return true;
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

