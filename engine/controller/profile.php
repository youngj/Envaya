<?php

class Controller_Profile extends Controller
{
    protected $org;
    protected $user;

    function before()
    {
        $user = get_user_by_username($this->request->param('username'));
        if ($user)
        {
            $this->user = $user;

            if ($user instanceof Organization)
            {
                $this->org = $user;
            }
            set_page_owner($user->guid);
        }
        else
        {
            not_found();
        }
    }

    function require_org()
    {
        if (!$this->org)
        {
            not_found();
        }
    }

    function action_index()
    {
        $widgetName = $this->request->param('widgetname');

        switch ($widgetName)
        {
            case "settings":    return $this->edit_settings();
            default:            break;
        }

        if ($this->org)
        {
            switch ($widgetName)
            {
                case "design":      return $this->edit_design();
                case "help":        return $this->view_help();
                case "feed":        return $this->view_feed();
                case "dashboard":   return $this->view_dashboard();
                case "confirm":     return $this->confirm_partner();
                case "compose":     return $this->compose_message();
                case "username":    return $this->change_username();
                default:
                    $widget = $this->org->getWidgetByName($widgetName);
                    return $this->view_widget($widget);
            }
        }
        else if ($widgetName == 'home')
        {
            return $this->edit_settings();
        }
        else
        {
            not_found();
        }
    }

    function action_edit()
    {
        $this->require_editor();

        $org = $this->org;
        $widgetName = $this->request->param('widgetname');
        $widget = $org->getWidgetByName($widgetName);

        $widgetTitle = elgg_echo("widget:{$widget->widget_name}");

        if ($widget->guid && $widget->isEnabled())
        {
            $title = sprintf(elgg_echo("widget:edittitle"), $widgetTitle);
        }
        else
        {
            $title = sprintf(elgg_echo("widget:edittitle:new"), $widgetTitle);
        }

        $cancelUrl = get_input('from') ?: $widget->getUrl();

        add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');

        $body = elgg_view_layout('one_column',
            elgg_view_title($title), $widget->renderEdit());

        $this->page_draw($title, $body);
    }

    function use_public_layout()
    {
        $org = $this->org;
        global $CONFIG;
        $CONFIG->sitename = $org->name;

        set_theme(get_input("__theme") ?: $org->theme ?: 'green');
        add_org_menu($org);
        set_context('orgprofile');
    }

    function use_editor_layout()
    {
        set_theme('editor');
        set_context('editor');
    }

    function require_editor()
    {
        gatekeeper();

        $user = $this->user;

        if ($user && $user->canEdit())
        {
            $this->use_editor_layout();

            return;
        }
        else if ($user)
        {
            force_login();
        }
        else
        {
            not_found();
        }
    }

    function edit_design()
    {
        $this->require_editor();
        $org = $this->org;

        $cancelUrl = get_input('from') ?: $org->getUrl();

        add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');

        $title = elgg_echo("design:edit");
        $area1 = elgg_view("org/design", array('entity' => $org));
        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function view_widget($widget)
    {
        $org = $this->org;

        $this->use_public_layout();

        $viewOrg = $org->canView();

        if (!$widget || !$widget->isActive())
        {
            org_page_not_found($org);
        }
        else if ($widget->widget_name == 'home')
        {
            $subtitle = $org->getLocationText(false);
            $title = '';
        }
        else
        {
            $subtitle = elgg_echo("widget:{$widget->widget_name}");
            $title = $subtitle;
        }

        if ($org->canEdit())
        {
            add_submenu_item(elgg_echo("widget:edit"), $widget->getEditURL(), 'edit');
        }

        if (get_input("__topbar") != "0")
        {
            $org->showCantViewMessage();

            $preBody = '';

            if (isadminloggedin())
            {
                $preBody .= elgg_view("org/admin_box", array('entity' => $org));
            }

            if ($org->canCommunicateWith())
            {
                $preBody .= elgg_view("org/comm_box", array('entity' => $org));
            }

            if ($org->guid == get_loggedin_userid() && $org->approval == 0)
            {
                $preBody .= elgg_view("org/setupNextStep");
            }
        }

        if ($viewOrg)
        {
            $body = org_view_body($org, $subtitle, ($viewOrg ? $widget->renderView() : ''), $preBody);
        }
        else
        {
            $body = '';
        }

        $this->page_draw($title, $body);
    }

    function view_help()
    {
        $this->require_editor();

        $title = elgg_echo("help:title");
        $area = elgg_view("org/help", array('org' => $this->org));
        $body = elgg_view_layout('one_column_padded', elgg_view_title($title), $area);
        $this->page_draw($title, $body);
    }

    function view_dashboard()
    {
        $this->require_editor();

        $org = $this->org;
        if ($org->guid == get_loggedin_userid())
        {
            $title = elgg_echo("dashboard");
        }
        else
        {
            $title = sprintf(elgg_echo("dashboard:other_user"), $org->name);
        }

        $area1 = elgg_view("org/dashboard", array('org' => $org));
        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function change_username()
    {
        $this->require_editor();
        admin_gatekeeper();

        $title = elgg_echo('username:title');
        $area1 = elgg_view('org/changeUsername', array('org' => $this->org));
        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function view_feed()
    {
        $this->require_editor();
        $title = elgg_echo("feed:org");

        page_set_translatable(false);

        $area = elgg_view('org/orgfeed', array('org' => $this->org));

        $body = elgg_view_layout('one_column', elgg_view_title($title), $area);

        $this->page_draw($title, $body);
    }

    function confirm_partner()
    {
        $this->require_editor();

        $partner_guid = get_input('partner_guid');
        $partner = get_entity($partner_guid);
        $org = $this->org;

        if ($partner)
        {
            $partnership = $org->getPartnership($partner);
            if ($partnership->isSelfApproved() || !$partnership->isPartnerApproved())
            {
                not_found();
            }

            $title = elgg_echo("partner:confirm");
            $area1 = elgg_view("org/confirmPartner", array('entity' => $org, 'partner' => $partner));
            $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);
            $this->page_draw($title,$body);
        }
        else
        {
            not_found();
        }
    }

    function compose_message()
    {
        gatekeeper();
        $this->use_editor_layout();
        $org = $this->org;

        if (!get_loggedin_user()->isApproved())
        {
            register_error(elgg_echo('message:needapproval'));
            forward_to_referrer();
        }

        add_submenu_item(elgg_echo("message:cancel"), $org->getURL(), 'edit');

        $title = elgg_echo("message:title");
        $area1 = elgg_view("org/composeMessage", array('entity' => $org));
        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);
        $this->page_draw($title,$body);
    }

    function edit_settings()
    {
        $this->require_editor();

        $title = elgg_echo("usersettings:user");

        $body = elgg_view_layout("one_column", elgg_view_title($title),
            elgg_view("usersettings/form", array('user' => $this->user)));

        return $this->page_draw($title, $body);
    }
}