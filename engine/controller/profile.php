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
        }
        else
        {
            not_found();
        }
    }

    function action_index()
    {
        $widgetName = $this->request->param('widgetname');

        if (!$this->org && $widgetName == 'home')
        {
            $widgetName = 'settings';
        }

        $methodName = "index_$widgetName";
        if (method_exists($this,$methodName))
        {
            return $this->$methodName();
        }
        
        if ($this->org)
        {
            $widget = $this->org->getWidgetByName($widgetName);            
            return $this->index_widget($widget);
        }       
        else
        {
            not_found();
        }
    }

    function action_save()
    {
        $this->validate_security_token();

        if (!$this->user->canEdit())
        {
            action_error(__('org:cantedit'));
        }

        $widgetName = $this->request->param('widgetname');

        $methodName = "save_$widgetName";
        if (method_exists($this,$methodName))
        {
            $this->$methodName();
        }
        else
        {
            if ($this->org)
            {
                $widget = $this->org->getWidgetByName($widgetName);
                $this->save_widget($widget);
            }
            else
            {       
                not_found();
            }
        }

        forward(get_input('from') ?: $this->user->getURL());
    }
    
    function action_options()
    {
        $this->require_admin();
        $this->require_org();
        $this->use_editor_layout();
        
        PageContext::set_translatable(false);
        
        $widgetName = $this->request->param('widgetname');
        $widget = $this->org->getWidgetByName($widgetName);
        
        $title = __('widget:options');
        $body = view('widgets/options', array('widget' => $widget));
        
        $this->page_draw($title, view_layout("one_column", view_title($title), $body));        
    }
    
    function action_save_options()
    {
        $this->require_admin();
        $this->require_org();
        $this->validate_security_token();
        
        $widgetName = $this->request->param('widgetname');
        $widget = $this->org->getWidgetByName($widgetName);
        
        $widget->handler_class = get_input('handler_class');
        $widget->handler_arg = get_input('handler_arg');
        $widget->title = get_input('title');
        $widget->menu_order = (int)get_input('menu_order');
        $widget->in_menu = get_input('in_menu') == 'no' ? 0 : 1;
        $widget->save();

        forward($widget->getURL());
    }

    function action_edit()
    {
        PageContext::set_translatable(false);
        $this->require_editor();
        $this->require_org();

        $org = $this->org;
        $widgetName = $this->request->param('widgetname');

        $widget = $org->getWidgetByName($widgetName);

        $widgetTitle = $widget->getTitle();

        if ($widget->guid && $widget->isEnabled())
        {
            $title = sprintf(__("widget:edittitle"), $widgetTitle);
        }
        else
        {
            $title = sprintf(__("widget:edittitle:new"), $widgetTitle);
        }

        $cancelUrl = get_input('from') ?: $widget->getUrl();

        add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $body = view_layout('one_column',
            view_title($title), $widget->renderEdit());

        $this->page_draw($title, $body);
    }

    function use_public_layout()
    {
        $org = $this->org;
        global $CONFIG;
        $CONFIG->sitename = $org->name;

        PageContext::set_theme(get_input("__theme") ?: $org->theme ?: 'green');
        
        foreach ($org->getAvailableWidgets() as $widget)
        {
            if ($widget->isActive() && $widget->in_menu)
            {
                add_submenu_item($widget->getTitle(), rewrite_to_current_domain($widget->getURL()));
            }
        }        
        
        $this->page_draw_vars['loginToCurrentPage'] = true;
    }

    function use_editor_layout()
    {
        PageContext::set_theme('editor');
    }

    function require_editor()
    {
        $this->require_login();

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

    function require_org()
    {
        if (!$this->org)
        {
            not_found();
        }
    }

    function index_design()
    {
        $this->require_editor();
        $org = $this->org;

        $cancelUrl = get_input('from') ?: $org->getUrl();

        add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $title = __("design:edit");
        $area1 = view("org/design", array('entity' => $org));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function save_design()
    {
        $this->require_org();
        $org = $this->org;

        $theme = get_input('theme');

        if ($theme != $org->theme)
        {
            system_message(__("theme:changed"));
            $org->theme = $theme;
            $org->save();
        }

        $iconFiles = get_uploaded_files($_POST['icon']);

        if (get_input('deleteicon'))
        {
            $org->setIcon(null);
            system_message(__("icon:reset"));
        }
        else if ($iconFiles)
        {
            $org->setIcon($iconFiles);
            system_message(__("icon:saved"));
        }

        $headerFiles = get_uploaded_files($_POST['header']);

        $customHeader = (int)get_input('custom_header');

        if (!$customHeader)
        {
            if ($org->custom_header)
            {
                $org->setHeader(null);
                system_message(__("header:reset"));
            }
        }
        else if ($headerFiles)
        {
            $org->setHeader($headerFiles);
            system_message(__("header:saved"));
        }
    }

    function index_widget($widget)
    {
        $org = $this->org;

        $this->use_public_layout();

        $viewOrg = $org->canView();

        if ($widget && $widget->widget_name == 'home')
        {
            $subtitle = $widget->title ? $widget->translate_field('title', false) : $org->getLocationText(false);
            $title = '';
        }
        else if (!$widget || !$widget->isActive())
        {
            $this->org_page_not_found();
        }
        else
        {
            $subtitle = $widget->getTitle();
            $title = $subtitle;
        }

        if ($org->canEdit())
        {
            add_submenu_item(__("widget:edit"), $widget->getEditURL(), 'edit');
        }

        if ($viewOrg)
        {
            $body = $this->org_view_body($subtitle, ($viewOrg ? $widget->renderView() : ''));
        }
        else
        {
            $org->showCantViewMessage();
            $body = '';
        }

        $this->page_draw($title, $body);
    }
    
    function get_pre_body()
    {
        $org = $this->org;
        $preBody = '';

        if (get_input("__topbar") != "0")
        {
            $org->showCantViewMessage();
        
            if (Session::isadminloggedin())
            {
                $preBody .= view("admin/org_actions", array('entity' => $org, 'widget' => $widget));
            }

            if ($org->canCommunicateWith())
            {
                $preBody .= view("org/comm_box", array('entity' => $org));
            }

            if ($org->guid == Session::get_loggedin_userid())
            {
                $preBody .= view("org/setupNextStep", array('entity' => $org));
            }
        }    
        return $preBody;
    }

    function save_widget($widget)
    {
        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            system_message(__('widget:delete:success'));

            forward($this->user->getURL());
        }
        else
        {
            if (!$widget->isEnabled())
            {
                $widget->enable();
            }

            try
            {
                $widget->saveInput();
                system_message(__('widget:save:success'));
                forward($widget->getURL());
            }
            catch (Exception $ex)
            {
                action_error($ex->getMessage());
            }
        }
    }

    function index_help()
    {
        $this->require_editor();
        $this->require_org();

        $title = __("help:title");
        $area = view("org/help", array('org' => $this->org));
        $body = view_layout('one_column_padded', view_title($title), $area);
        $this->page_draw($title, $body);
    }

    function index_dashboard()
    {    
        $this->require_editor();        
        
        PageContext::set_translatable(false);

        $user = $this->user;
        if ($user->guid == Session::get_loggedin_userid())
        {
            $title = __('dashboard:title');
        }
        else
        {
            $title = sprintf(__("dashboard:other_user"), $user->name);
        }
                
        $org = $this->org;
        if ($org)
        {            
            $area1 = view("org/dashboard", array('org' => $org));
            $area2 = view("org/setupNextStep", array('entity' => $org));                 
        }
        else if ($user->admin)
        {
            $area1 = view('admin/dashboard');
            $area2 = '';
        }
        else
        {
            $area1 = "<div class='padded'>You are not an organization!</div>";
            $area2 = '';
        }
        
        $body = view_layout("one_column", view_title($title), $area1, $area2);
        $this->page_draw($title,$body);
    }

    function index_username()
    {
        $this->require_editor();
        $this->require_org();
        $this->require_admin();

        $title = __('username:title');
        $area1 = view('org/changeUsername', array('org' => $this->org));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function save_username()
    {
        $this->require_org();
        $org = $this->org;

        $username = get_input('username');

        $oldUsername = $org->username;

        if ($username && $username != $oldUsername)
        {
            try
            {
                validate_username($username);
            }
            catch (RegistrationException $ex)
            {
                register_error($ex->getMessage());
                forward_to_referrer();
            }

            if (get_user_by_username($username))
            {
                register_error(__('registration:userexists'));
                forward_to_referrer();
            }

            $org->username = $username;
            $org->save();

            get_cache()->delete(get_cache_key_for_username($oldUsername));

            system_message(__('username:changed'));
        }
        forward($org->getURL());
    }

    function index_feed()
    {
        $this->require_editor();
        $this->require_org();

        $title = __("feed:org");

        PageContext::set_translatable(false);

        $area = view('org/orgfeed', array('org' => $this->org));

        $body = view_layout('one_column', view_title($title), $area);

        $this->page_draw($title, $body);
    }

    function index_compose()
    {
        $this->require_login();
        $this->use_editor_layout();
        $this->require_org();

        $org = $this->org;

        if (!Session::get_loggedin_user()->isApproved())
        {
            register_error(__('message:needapproval'));
            forward_to_referrer();
        }

        add_submenu_item(__("message:cancel"), $org->getURL(), 'edit');

        $title = __("message:title");
        $area1 = view("org/composeMessage", array('entity' => $org));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }

    function index_settings()
    {
        $this->require_editor();

        $title = __("usersettings:user");

        $body = view_layout("one_column", view_title($title),
            view("usersettings/form", array('entity' => $this->user)));

        return $this->page_draw($title, $body);
    }

    function save_settings()
    {
        $user = $this->user;

        $name = get_input('name');

        if ($name)
        {
            if (strcmp($name, $user->name)!=0)
            {
                $user->name = $name;
                system_message(__('user:name:success'));
            }
        }
        else
        {
            action_error(__('create:no_name'));
        }

        $password = get_input('password');
        $password2 = get_input('password2');
        if ($password!="")
        {
            try
            {
                validate_password($password);
            }
            catch (RegistrationException $ex)
            {
                action_error($ex->getMessage());
            }

            if ($password == $password2)
            {
                $user->setPassword($password);
                system_message(__('user:password:success'));
            }
            else
            {
                action_error(__('user:password:fail:notsame'));
            }
        }

        $language = get_input('language');
        if ($language && $language != $user->language)
        {
            $user->language = $language;
            change_viewer_language($user->language);
            system_message(__('user:language:success'));
        }

        $email = trim(get_input('email'));
        if ($email != $user->email)
        {
            try
            {
                validate_email_address($email);
            }
            catch (RegistrationException $ex)
            {
                action_error($ex->getMessage());
            }

            $user->email = $email;
            system_message(__('user:email:success'));
        }

        $phone = get_input('phone');
        if ($phone != $user->phone_number)
        {
            $user->phone_number = $phone;
            system_message(__('user:phone:success'));
        }

        if ($user instanceof Organization)
        {
            $notify_days = get_input('notify_days');
            if ($notify_days != $user->notify_days)
            {
                $user->notify_days = $notify_days;
                system_message(__('user:notification:success'));
            }
        }

        $user->save();
    }

    function index_addphotos()
    {
        $this->require_org();
        $this->require_editor();
        
        $title = __('addphotos:title');
        $area1 = view('org/addPhotos', array('entity' => $this->org));
        
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }
    
    function save_addphotos()
    {
        $this->require_org();
        $this->require_editor();
        $this->validate_security_token();
        
        $imageNumbers = get_input_array('imageNumber');
        
        $uuid = get_input('uuid');
        $org = $this->org;
        
        $duplicates = NewsUpdate::queryByMetadata('uuid', $uuid)->where('container_guid=?',$org->guid)->filter();
        
        foreach ($imageNumbers as $imageNumber)
        {
            $imageData = get_input('imageData'.$imageNumber);
            $imageCaption = get_input('imageCaption'.$imageNumber);

            $images = get_uploaded_files($imageData);
            $image = @$images['large'] ?: @$images['medium'] ?: @$images['small'];
            
            $body = "<p><img class='image_center' src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' /></p>";
            if ($imageCaption)
            {
                $body .= "<p>".view('input/longtext', array('value' => $imageCaption))."</p>";
            }
                        
            $post = new NewsUpdate();
            $post->owner_guid = Session::get_loggedin_userid();
            $post->container_guid = $org->guid;
            $post->setContent($body, true);
            $post->uuid = $uuid;
            $post->save();                                  
        }
        
        system_message(__('addphotos:success'));
        forward($org->getURL()."/news");
    }
    
    function index_confirm_partner()
    {
        $this->require_org();
        $this->require_login();

        $partner = $this->org;

        $org = Session::get_loggedin_user();
        if (!$org instanceof Organization)
        {
            not_found();
        }

        if ($partner)
        {
            $partnership = $org->getPartnership($partner);
            if ($partnership->isSelfApproved() || !$partnership->isPartnerApproved())
            {
                not_found();
            }

            $title = __("partner:confirm");
            $area1 = view("org/confirmPartner", array('entity' => $org, 'partner' => $partner));
            $body = view_layout("one_column", view_title($title), $area1);
            $this->page_draw($title,$body);
        }
        else
        {
            not_found();
        }
    }

    function index_request_partner()
    {
        $this->require_org();
        $this->require_login();
        $this->validate_security_token();

        global $CONFIG;

        $partner = $this->org;

        $loggedInOrg = Session::get_loggedin_user();

        if (!$loggedInOrg->isApproved())
        {
            action_error(__('partner:needapproval'));
        }

        if (!$partner || $partner_guid == $loggedInOrg->guid)
        {
            register_error(__("partner:invalid"));
            forward();
        }
        else
        {
            $partnership = $loggedInOrg->getPartnership($partner);
            $partnership->setSelfApproved(true);
            $partnership->save();

            $partnership2 = $partner->getPartnership($loggedInOrg);
            $partnership2->setPartnerApproved(true);
            $partnership2->save();

            $partner->notify(
                sprintf(__('email:requestPartnership:subject',$partner->language), $loggedInOrg->name, $partner->name),
                sprintf(__('email:requestPartnership:body',$partner->language), $partnership->getApproveUrl())
            );

            system_message(__("partner:request_sent"));

            forward($partner->getUrl());
        }
    }

    function index_create_partner()
    {
        $this->require_org();
        $this->require_login();
        $this->validate_security_token();

        $user = Session::get_loggedin_user();

        $partner = $this->org;

        if (!$partner || $partner_guid == $user->guid)
        {
            register_error(__("partner:invalid"));
            forward();
        }
        else
        {
            $partnership = $partner->getPartnership($user);
            $partnership->setPartnerApproved(true);
            $partnership->save();

            $partnership2 = $user->getPartnership($partner);
            $partnership2->setSelfApproved(true);
            $partnership2->save();

            $partWidget = $user->getWidgetByName('partnerships');
            $partWidget->save();

            $partWidget2 = $partner->getWidgetByName('partnerships');
            $partWidget2->save();

            system_message(__("partner:created"));

            post_feed_items($user, 'partnership', $partner);

            $partner->notify(
                sprintf(__('email:partnershipConfirmed:subject',$partner->language), $user->name, $partner->name),
                sprintf(__('email:partnershipConfirmed:body',$partner->language), $partWidget2->getURL())
            );

            forward($partWidget->getURL());
        }
    }

    function index_send_message()
    {
        $this->require_org();
        $this->require_login();
        $this->validate_security_token();

        $user = Session::get_loggedin_user();

        $recipient = $this->org;

        if (!$recipient || !$user->isApproved())
        {
            register_error(__("message:invalid_recipient"));
            forward();
        }
        else
        {
            $subject = get_input('subject');
            if (!$subject)
            {
                action_error(__("message:subject_missing"));
            }

            $message = get_input('message');
            if (!$message)
            {
                action_error(__("message:message_missing"));
            }

            $headers = array(
                'To' => $recipient->getNameForEmail(),
                'From' => $user->getNameForEmail(),
                'Reply-To' => $user->getNameForEmail(),
                'Bcc' => $user->getNameForEmail(),
            );

            send_mail($recipient->email, $subject, $message, $headers);

            system_message(__("message:sent"));

            forward($recipient->getURL());
        }
    }
    
    function org_page_not_found()
    {
        $org = $this->org;
        if ($org)
        {    
            $title = __('page:notfound');
            $body = $this->org_view_body($title, "<div class='section_content padded'>".__('page:notfound:details')."</div>");
            header("HTTP/1.1 404 Not Found");
            echo page_draw($title, $body);
        }
        else
        {
            not_found();
        }
        exit;
    }   
    
    function org_view_body($subtitle, $area2)
    {
        $org = $this->org;
    
        if ($org->custom_header)
        {
            $header = view('org/custom_header', array(
                'org' => $org
            ));
        }
        else
        {
            $header = view('org/default_header', array(
                'org' => $org,
                'subtitle' => $subtitle,
            ));
        }

        $layout = "one_column_custom_header";
        if (PageContext::get_theme() == 'sidebar')
        {
            $layout= 'two_column_left_sidebar';
        }
        
        return view_layout($layout, $header, $area2, $this->get_pre_body());
    }


}