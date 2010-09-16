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
            $widget = $this->org->get_widget_by_name($widgetName);            
            return $this->index_widget($widget);
        }       
        else
        {
            not_found();
        }
    }
    
    function show_cant_view_message()
    {
        if ($this->org->approval == 0)
        {
            system_message(__('approval:waiting'));
        }
        else if ($this->org->approval < 0)
        {
            system_message(__('approval:rejected'));
        }
    }    

    function action_save()
    {
        $this->validate_security_token();

        if (!$this->user->can_edit())
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
                $widget = $this->org->get_widget_by_name($widgetName);
                $this->save_widget($widget);
            }
            else
            {       
                not_found();
            }
        }

        forward(get_input('from') ?: $this->user->get_url());
    }
    
    function action_options()
    {
        $this->require_admin();
        $this->require_org();
        $this->use_editor_layout();
        
        PageContext::set_translatable(false);
        
        $widgetName = $this->request->param('widgetname');
        $widget = $this->org->get_widget_by_name($widgetName);
        
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
        $widget = $this->org->get_widget_by_name($widgetName);
        
        $widget->handler_class = get_input('handler_class');
        $widget->handler_arg = get_input('handler_arg');
        $widget->title = get_input('title');
        $widget->menu_order = (int)get_input('menu_order');
        $widget->in_menu = get_input('in_menu') == 'no' ? 0 : 1;
        $widget->save();

        forward($widget->get_url());
    }

    function action_edit()
    {
        PageContext::set_translatable(false);
        $this->require_editor();
        $this->require_org();

        $org = $this->org;
        $widgetName = $this->request->param('widgetname');

        $widget = $org->get_widget_by_name($widgetName);

        $widgetTitle = $widget->get_title();

        if ($widget->guid && $widget->is_enabled())
        {
            $title = sprintf(__("widget:edittitle"), $widgetTitle);
        }
        else
        {
            $title = sprintf(__("widget:edittitle:new"), $widgetTitle);
        }

        $cancelUrl = get_input('from') ?: $widget->get_url();

        add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $body = view_layout('one_column',
            view_title($title), $widget->render_edit());

        $this->page_draw($title, $body);
    }

    function use_public_layout()
    {
        $org = $this->org;
        global $CONFIG;
        $CONFIG->sitename = $org->name;

        PageContext::set_theme(get_input("__theme") ?: $org->theme ?: 'green');
        
        foreach ($org->get_available_widgets() as $widget)
        {
            if ($widget->is_active() && $widget->in_menu)
            {
                add_submenu_item($widget->get_title(), rewrite_to_current_domain($widget->get_url()));
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

        if ($user && $user->can_edit())
        {
            $this->use_editor_layout();

            return;
        }
        else if ($user)
        {
            if (Session::isloggedin())
            {
                register_error(__('noaccess'));
            }        
        
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

        $cancelUrl = get_input('from') ?: $org->get_url();

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
            $org->set_icon(null);
            system_message(__("icon:reset"));
        }
        else if ($iconFiles)
        {
            $org->set_icon($iconFiles);
            system_message(__("icon:saved"));
        }

        $headerFiles = get_uploaded_files($_POST['header']);

        $customHeader = (int)get_input('custom_header');

        if (!$customHeader)
        {
            if ($org->custom_header)
            {
                $org->set_header(null);
                system_message(__("header:reset"));
            }
        }
        else if ($headerFiles)
        {
            $org->set_header($headerFiles);
            system_message(__("header:saved"));
        }
    }

    function index_widget($widget)
    {
        $org = $this->org;

        $this->use_public_layout();

        $viewOrg = $org->can_view();

        if ($widget && $widget->widget_name == 'home')
        {
            $subtitle = $widget->title ? $widget->translate_field('title', false) : $org->get_location_text(false);
            $title = '';
        }
        else if (!$widget || !$widget->is_active())
        {
            $this->org_page_not_found();
        }
        else
        {
            $subtitle = $widget->get_title();
            $title = $subtitle;
        }

        if ($org->can_edit())
        {
            add_submenu_item(__("widget:edit"), $widget->get_edit_url(), 'edit');
            add_submenu_item(__('widget:options'), "{$widget->get_base_url()}/options", 'org_actions');
        }

        if ($viewOrg)
        {
            $body = $this->org_view_body($subtitle, ($viewOrg ? $widget->render_view() : ''));
        }
        else
        {
            $this->show_cant_view_message();
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
            $this->show_cant_view_message();
        
            if (Session::isadminloggedin())
            {
                $preBody .= view("admin/org_actions", array('entity' => $org));
            }

            if ($org->can_view() && Session::isloggedin() && Session::get_loggedin_userid() != $org->guid)
            {
                $preBody .= view("org/comm_box", array('entity' => $org));
            }

            if ($this->show_next_steps())
            {
                $preBody .= view("org/setupNextStep", array('entity' => $org));
            }
        }    
        return $preBody;
    }

    function show_next_steps()
    {
        return $this->org->guid == Session::get_loggedin_userid();
    }
    
    function save_widget($widget)
    {
        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            system_message(__('widget:delete:success'));

            forward($this->user->get_url());
        }
        else
        {
            if (!$widget->is_enabled())
            {
                $widget->enable();
            }

            try
            {             
                $widget->save_input();
            }
            catch (Exception $ex)
            {
                action_error($ex->getMessage());
            }
            
            system_message(__('widget:save:success'));
            forward($widget->get_url());
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
        forward($org->get_url());
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

        if (!Session::get_loggedin_user()->is_approved())
        {
            register_error(__('message:needapproval'));
            forward_to_referrer();
        }

        add_submenu_item(__("message:cancel"), $org->get_url(), 'edit');

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
                $user->set_password($password);
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
            $enable_batch_email = get_input('enable_batch_email');
            if ($enable_batch_email != $user->enable_batch_email)
            {
                $user->enable_batch_email = $enable_batch_email;
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
        
        $duplicates = NewsUpdate::query_by_metadata('uuid', $uuid)->where('container_guid=?',$org->guid)->filter();
        
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
            $post->set_content($body, true);
            $post->uuid = $uuid;
            $post->save();                                  
        }
        
        system_message(__('addphotos:success'));
        forward($org->get_url()."/news");
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
            $partnership = $org->get_partnership($partner);
            if ($partnership->is_self_approved() || !$partnership->is_partner_approved())
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

        if (!$loggedInOrg->is_approved())
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
            $partnership = $loggedInOrg->get_partnership($partner);
            $partnership->set_self_approved(true);
            $partnership->save();

            $partnership2 = $partner->get_partnership($loggedInOrg);
            $partnership2->set_partner_approved(true);
            $partnership2->save();

            $partner->notify(
                sprintf(__('email:requestPartnership:subject',$partner->language), $loggedInOrg->name, $partner->name),
                sprintf(__('email:requestPartnership:body',$partner->language), $partnership->get_approve_url())
            );

            system_message(__("partner:request_sent"));

            forward($partner->get_url());
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
            $partnership = $partner->get_partnership($user);
            $partnership->set_partner_approved(true);
            $partnership->save();

            $partnership2 = $user->get_partnership($partner);
            $partnership2->set_self_approved(true);
            $partnership2->save();

            $partWidget = $user->get_widget_by_name('partnerships');
            $partWidget->save();

            $partWidget2 = $partner->get_widget_by_name('partnerships');
            $partWidget2->save();

            system_message(__("partner:created"));

            post_feed_items($user, 'partnership', $partner);

            $partner->notify(
                sprintf(__('email:partnershipConfirmed:subject',$partner->language), $user->name, $partner->name),
                sprintf(__('email:partnershipConfirmed:body',$partner->language), $partWidget2->get_url())
            );

            forward($partWidget->get_url());
        }
    }

    function index_send_message()
    {
        $this->require_org();
        $this->require_login();
        $this->validate_security_token();

        $user = Session::get_loggedin_user();

        $recipient = $this->org;

        if (!$recipient || !$user->is_approved())
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
                'To' => $recipient->get_name_for_email(),
                'From' => $user->get_name_for_email(),
                'Reply-To' => $user->get_name_for_email(),
                'Bcc' => $user->get_name_for_email(),
            );

            send_mail($recipient->email, $subject, $message, $headers);

            system_message(__("message:sent"));

            forward($recipient->get_url());
        }
    }
    
    function index_domains()
    {
        $this->require_org();
        $this->require_admin();
        $this->use_editor_layout();
        $title = __('domains:edit');
        $area1 = view('org/domains', array('org' => $this->org));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }
    
    function index_add_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $domain_name = get_input('domain_name');
        if (OrgDomainName::query()->where('domain_name = ?', $domain_name)->count() > 0)
        {
            action_error(__('domains:duplicate'));
        }
        if (preg_match('/[^\w\.\-]/', $domain_name))
        {
            action_error(__('domains:invalid'));
        }
        
        $org_domain_name = new OrgDomainName();
        $org_domain_name->domain_name = $domain_name;
        $org_domain_name->guid = $this->org->guid;
        $org_domain_name->save();
        system_message(__('domains:added'));
        forward_to_referrer();
    }
    
    function index_delete_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $org_domain_name = OrgDomainName::query()->where('id = ?', (int)get_input('id'))->get();
        if (!$org_domain_name)
        {
            action_error(__('domains:not_found'));
        }
        $org_domain_name->delete();
        system_message(__('domains:deleted'));
        forward_to_referrer();
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