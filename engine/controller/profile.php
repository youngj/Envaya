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
        else if ($this->org)
        {
            $widget = $this->org->get_widget_by_name($widgetName);                        
            return $this->index_widget($widget);
        }       
        return not_found();
    }
    
    function index_widget($widget)
    {
        $org = $this->org;
    
        $this->require_http();

        $show_menu = true;
        if (get_viewtype() == 'mobile' && $widget && $widget->widget_name != 'home')
        {
            $show_menu = false;
        }
        
        $this->use_public_layout($show_menu);

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
            PageContext::add_submenu_item(__("widget:edit"), $widget->get_edit_url(), 'edit');
            PageContext::add_submenu_item(__('widget:options'), "{$widget->get_base_url()}/options", 'org_actions');
        }

        if ($viewOrg)
        {
            $body = $this->org_view_body($subtitle, ($viewOrg ? view('widgets/view', array('widget' => $widget)) : ''));
        }
        else
        {
            $this->show_cant_view_message();
            $body = '';
        }

        $this->page_draw($title, $body);
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

    function action_edit()
    {
        // backwards compatibility to avoid breaking links and allow editing widgets
        // at /<username>/<widgetname>/edit         
        // by forwarding to new URLs at /<username>/page/<widgetname>/edit         
     
        $widgetName = $this->request->param('widgetname');
        $widget = $this->org->get_widget_by_name($widgetName);
        if ($widget->is_active())
        {
            forward($widget->get_edit_url());
        }
        else
        {
            not_found();
        }
    }
    
    function use_public_layout($show_menu = true)
    {
        $org = $this->org;
        
        $this->page_draw_vars['sitename'] = $org->name;

        PageContext::set_theme(get_input("__theme") ?: $org->theme ?: 'green');
        PageContext::set_site_org($org);
        
        if ($show_menu)
        {
            foreach ($org->get_available_widgets() as $widget)
            {
                if ($widget->is_active() && $widget->in_menu)
                {
                    PageContext::add_submenu_item($widget->get_title(), rewrite_to_current_domain($widget->get_url()));
                }
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

    function index_add_page()
    {
        $this->require_editor();  

        if (Request::is_post())
        {
            $this->save_add_page();
        }
        
        $org = $this->org;
        
        $cancelUrl = get_input('from') ?: $org->get_url();
        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');
        
        $title = __("widget:new");
        
        $area1 = view("widgets/add", array('org' => $org));
        $body = view_layout("one_column_padded", view_title($title), $area1);

        $this->page_draw($title,$body);        
    }
    
    private function save_add_page()
    {
        $this->validate_security_token();
        $this->require_editor();
        
        $title = get_input('title');
        if (!$title)
        {
            return register_error(__('widget:no_title'));            
        }
        
        $widget_name = get_input('widget_name');
        if (!$widget_name || !Widget::is_valid_name($widget_name))
        {
            return register_error(__('widget:bad_name'));            
        }
        
        $widget = $this->org->get_widget_by_name($widget_name);
        
        if ($widget->guid && ((time() - $widget->time_created > 30) || !($widget->get_handler() instanceof WidgetHandler_Generic)))
        {
            return register_error_html(sprintf(__('widget:duplicate_name'),"<a href='{$widget->get_edit_url()}'><strong>".__('clickhere')."</strong></a>")); 
        }
        
        $widget->save_input();             
        
        system_message(__('widget:save:success'));
        
        forward($widget->get_url());        
    }
    
    function index_design()
    {
        $this->require_editor();
        
        if (Request::is_post())
        {
            $this->save_design();
        }
        
        $org = $this->org;

        $cancelUrl = get_input('from') ?: $org->get_url();

        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $title = __("design:edit");
        $area1 = view("org/design", array('entity' => $org));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    private function save_design()
    {        
        $this->validate_security_token();
        $this->require_org();
        $org = $this->org;

        $theme = get_input('theme');

        if ($theme != $org->theme)
        {
            system_message(__("theme:changed"));
            $org->theme = $theme;
            $org->save();
        }

        $iconFiles = UploadedFile::json_decode_array($_POST['icon']);

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

        $headerFiles = UploadedFile::json_decode_array($_POST['header']);

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
        
        forward($org->get_url());
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
        
        if (Request::is_post())
        {
            $this->save_username();
        }

        $title = __('username:title');
        $area1 = view('org/changeUsername', array('org' => $this->org));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function save_username()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        
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
                return register_error($ex->getMessage());
            }

            if (get_user_by_username($username))
            {
                return register_error(__('registration:userexists'));
            }

            $org->username = $username;
            $org->save();

            get_cache()->delete(get_cache_key_for_username($username));
            get_cache()->delete(get_cache_key_for_username($oldUsername));

            system_message(__('username:changed'));
        }
        forward($org->get_url());
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

        PageContext::add_submenu_item(__("message:cancel"), $org->get_url(), 'edit');

        $title = __("message:title");
        $area1 = view("org/composeMessage", array('entity' => $org, 'user' => Session::get_loggedin_user()));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }

    function index_settings()
    {    
        $this->require_https();
        $this->require_editor();
        
        if (Request::is_post())
        {
            $this->save_settings();
        }

        $title = __("usersettings:user");

        $body = view_layout("one_column", view_title($title),
            view("usersettings/form", array('entity' => $this->user)));

        return $this->page_draw($title, $body);
    }

    function save_settings()
    {
        $this->validate_security_token();
        
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
            return register_error(__('create:no_name'));
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
                return register_error($ex->getMessage());
            }

            if ($password == $password2)
            {
                $user->set_password($password);
                system_message(__('user:password:success'));
            }
            else
            {
                return register_error(__('user:password:fail:notsame'));
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
                return register_error($ex->getMessage());
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
            $notifications = get_bit_field_from_options(get_input_array('notifications'));
			
            if ($notifications != $user->notifications)
            {
                $user->notifications = $notifications;
                system_message(__('user:notification:success'));
            }
        }

        $user->save();
        forward($user->get_url());
    }

    function index_addphotos()
    {
        $this->require_org();
        $this->require_editor();
        
        if (Request::is_post())
        {
            $this->save_addphotos();
        }
        
        $title = __('addphotos:title');
        $area1 = view('org/addPhotos', array('entity' => $this->org));
        
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }
    
    private function save_addphotos()
    {
        $this->validate_security_token();
        
        $imageNumbers = get_input_array('imageNumber');
        
        $uuid = get_input('uuid');
        $org = $this->org;
        
        $duplicates = NewsUpdate::query()->with_metadata('uuid', $uuid)->where('container_guid=?',$org->guid)->filter();
        
        foreach ($imageNumbers as $imageNumber)
        {                        
            $imageData = get_input('imageData'.$imageNumber);
            
            if (!$imageData) // mobile version uploads image files when the form is submitted, rather than asynchronously via javascript
            {     
                $sizes = json_decode(get_input('sizes'));
                $images = UploadedFile::upload_images_from_input($_FILES['imageFile'.$imageNumber], $sizes);
            }
            else
            {
                $images = UploadedFile::json_decode_array($imageData);
            }
            
            $imageCaption = get_input('imageCaption'.$imageNumber);
            
            $image = $images[sizeof($images) - 1];
            
            $body = "<p><img class='image_center' src='{$image->get_url()}' width='{$image->width}' height='{$image->height}' /></p>";
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
            $post->post_feed_items();
        }
        
        system_message(__('addphotos:success'));
        forward($org->get_url()."/news");
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

            if ($recipient->send_mail($subject, $message, array(
                'From' => $user->get_name_for_email(),
                'Reply-To' => $user->get_name_for_email(),
                'Bcc' => $user->get_name_for_email(),            
            )))
            {
                system_message(__("message:sent"));
            }
            else
            {
                action_error(__("message:not_sent"));
            }

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
	
	function post_comment($entity)
	{    
		$comments_url = $entity->get_url()."?comments=1";
	
        $userId = Session::get_loggedin_userid();
        
        if ($userId)
        {
            $this->validate_security_token();
        }       
     
        $name = get_input('name');
        $content = get_input('content');
        $location = get_input('location');
        
        if (!$content)
        {   
            register_error(__('comment:empty'));
			Session::save_input();
			forward($comments_url);
        }
        
		if ($entity->query_comments()->where('content = ?', $content)->count() > 0)
		{
			register_error(__('comment:duplicate'));
			Session::save_input();
			forward($comments_url);
		}
		
        if (!$userId && Config::get('recaptcha_enabled'))
        {        
			$valid_captcha = false;
			if (get_input('captcha'))
			{
				$res = Recaptcha::check_answer();
				if ($res->is_valid)
				{
					$valid_captcha = true;
				}
				else
				{
					register_error(__('comment:captcha_invalid'));
				}
			}
		
			if (!$valid_captcha)
			{
				$title = __('comment:verify_human');
				$this->use_public_layout();
				$body = $this->org_view_body($title, view("org/comment_captcha"));
				$this->page_draw($title, $body);
				return;
			}
		}
		
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        
		$comment = new Comment();
		$comment->container_guid = $entity->guid;
		$comment->owner_guid = $userId;
		$comment->name = $name;
        $comment->location = $location;
		$comment->content = $content;
		$comment->language = GoogleTranslate::guess_language($content);
		$comment->save();
	
		$entity->num_comments = $entity->query_comments()->count();
		$entity->save();
	
		if (!$userId)
		{
			$posted_comments = Session::get('posted_comments') ?: array();
			$posted_comments[] = $comment->guid;
			Session::set('posted_comments', $posted_comments);
		}
		
		$org = $entity->get_root_container_entity();
		
		$notification_subject = sprintf(__('comment:notification_subject', $org->language), 
			$comment->get_name());
		$notification_body = sprintf(__('comment:notification_body', $org->language),
			$comment->content,
			"$comments_url#comments"
		);
		
		if ($org && $org->email && $org->is_notification_enabled(Notification::Comments) 
				&& $ownerGuid != $org->guid)
		{		
			$org->send_mail($notification_subject, $notification_body);
		}
		send_admin_mail(
			sprintf(__('comment:notification_admin_subject'), $comment->get_name(), $org->name), 
			$notification_body);
		
		system_message(__('comment:success'));
		forward($comments_url);
	}    
}