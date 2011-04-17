<?php

class Controller_Admin extends Controller
{
    function before()
    {
        $this->require_admin();
        $this->page_draw_vars['theme_name'] = 'editor';
    }

    function action_contact()
    {
        $this->page_draw(array(
            'theme_name' => 'simple_wide',
            'title' => __('email:send'),
            'header' => '',
            'content' => view('admin/contact')
        ));
    }

    function action_emails()
    {
        $emails = EmailTemplate::query()->filter();
        
        $this->page_draw(array(
            'title' => __('email:list'),
            'content' => view('admin/list_emails', array('emails' => $emails))
        ));        
    }

    function action_view_email()
    {
        $org = User::get_by_username(get_input('username'));
        
        PageContext::set_translatable(false);
               
        $email = EmailTemplate::get_by_guid(get_input('email')) ?: EmailTemplate::query()->where('active<>0')->get();
        if (!$email)
        {
            return $this->not_found();
        }

        $this->page_draw(array(
            'title' => __('email:view'),
            'content' => view('admin/view_email', array('org' => $org, 'email' => $email, 'from' => get_input('from')))
        ));                    
    }        
    
    function action_edit_email()
    {
        $email = EmailTemplate::get_by_guid(get_input('email'));
        if (!$email)
        {
            return $this->not_found();
        }

        $this->page_draw(array(
            'title' => __('email:edit'),
            'content' => view('admin/edit_email', array('email' => $email)),
        ));
    }
    
    function action_view_email_body()
    {
        $user = User::get_by_username(get_input('username'));
        $email = EmailTemplate::get_by_guid(get_input('email'));

        if (!$email)
        {
            return $this->not_found();
        }
        
        echo view('emails/template', array(
            'org' => $user, 
            'base' => 'http://ERROR_RELATIVE_URL/ERROR_RELATIVE_URL/', 
            'email' => $email
        ));            
    }
    
    function action_resend_mail()
    {
        $this->validate_security_token();
        
        $id = get_input('id');
        
        $mail = OutgoingMail::query()->where('id = ?', $id)->get();
        if (!$mail)
        {
            return $this->not_found();
        }        
        $mail->send(true);        
        SessionMessages::add(__('email:sent_ok'));
        forward('/admin/outgoing_mail');
    }
    
    function action_outgoing_mail()
    {
        $this->page_draw(array(
            'title' => __('email:outgoing_mail'),
            'content' => view('admin/outgoing_mail'),
            'theme_name' => 'simple_wide',
            'header' => '',
        ));        
    }
    
    function action_batch_email()
    {
        $email = EmailTemplate::get_by_guid(get_input('email')) ?: EmailTemplate::query()->where('active<>0')->get();        
        if (!$email)
        {
            return $this->not_found();
        }
        
        $org_guids = get_input_array('orgs');
        if ($org_guids)
        {
            $orgs = Organization::query()->where_in('guid', $org_guids)->filter();
        }
        else
        {         
            $orgs = Organization::query()
                ->where('approval > 0')
                ->where("email <> ''")
                ->where('(notifications & ?) > 0', Notification::Batch)
                ->where("not exists (select * from outgoing_mail where email_guid = ? and user_guid = users.guid)", $email->guid)
                ->order_by('guid')
                ->limit(50)
                ->filter(); 
        }

        $this->page_draw(array(
            'title' => __('email:batch'),
            'content' => view('admin/batch_email', array('email' => $email, 'orgs' => $orgs)),
        ));        
    }

    function action_send_batch_email()
    {
        $this->validate_security_token();
        
        $email = EmailTemplate::get_by_guid(get_input('email'));
        $org_guids = get_input_array('orgs');
        $numSent = 0;
        foreach ($org_guids as $org_guid)
        {       
            $org = Organization::get_by_guid($org_guid);

            if ($email->can_send_to($org))
            {
                $numSent++;
                $email->send_to($org);
            }
        }
        SessionMessages::add("sent $numSent emails");
        forward(get_input('from') ?: "/admin/batch_email?email={$email->guid}");
    }
    
    
    function action_send_email()
    {
        $this->validate_security_token();
        
        $email = EmailTemplate::get_by_guid(get_input('email'));
        $org = Organization::get_by_guid(get_input('org_guid'));
        
        if ($email->can_send_to($org))
        {
            $email->send_to($org);
            SessionMessages::add(__('email:sent'));
        }
        else
        {
            SessionMessages::add_error(__('email:none_sent'));
        }

        forward(get_input('from') ?: "/admin/contact");
    }

    function action_translateQueue()
    {
        $this->page_draw(array(
            'title' => __('translate:queue'),
            'content' => view('translation/queue', array('lang' => Language::get_current_code())),
            'theme_name' => 'simple_wide',
        ));
    }

    function action_statistics()
    {
        $this->page_draw(array(
            'title' => __("admin:statistics"),
            'content' => view("admin/statistics")
        ));
    }

    function action_user()
    {
        $search = get_input('s');
        $limit = get_input('limit', 10);
        $offset = get_input('offset', 0);
        
        $count = User::query()->count();
        $entities = User::query()->limit($limit, $offset)->order_by('guid desc')->filter();

        $result = view('paged_list', array(
            'entities' => $entities,
            'count' => $count,
            'offset' => $offset,
            'limit' => $limit,
        ));        

        $this->page_draw(array(
            'title' => __("admin:user"),
            'content' => view("admin/user", array('list' => $result)),
        ));        
    }

    function action_search()
    {
        $tag = get_input('tag');
        
        $limit = 10;
        $offset = (int)get_input('offset');

        $object = get_input('object');
       
        $query = User::query()->where('(INSTR(username, ?) > 0 OR INSTR(name, ?) > 0)', $tag, $tag);
       
        $users = $query->limit($limit, $offset)->filter();
        $count = $query->count();
        
        if ($users)
        {
            $content = view('search/results_list', array(
                'entities' => $users,
                'count' => $count,
                'offset' => $offset,
                'limit' => $limit,
            ));            
        }
        else
        {
            $content = __('search:no_results');
        }
                
        $this->page_draw(array(
            'title' => sprintf(__('search:title_with_query'),$tag),
            'content' => view('section', array('content' => $content)),
        ));
    }
    
    function action_logbrowser()
    {
        $query = SystemLog::query();
    
        $limit = get_input('limit', 40);
        $offset = get_input('offset');

        $search_username = get_input('search_username');
        if ($search_username) {
            if ($user = User::get_by_username($search_username)) {
                $user = $user->guid;
            }
        } else {
            $user_guid = get_input('user_guid',0);
            if ($user_guid) {
                $user = (int) $user_guid;
            } else {
                $user = "";
            }
        }

        $timelower = get_input('timelower');
        if ($timelower) 
        {
            $query->where('time_created > ?', strtotime($timelower));
        }
        $timeupper = get_input('timeupper');
        if ($timeupper) 
        {
            $query->where('time_created < ?', strtotime($timeupper));
        }

        if ($user)
        {
            $query->where('user_guid=?', $user);
        }
                
        $query->limit($limit, $offset);
        
        $log = $query->filter();
        $count = $query->count();

        $this->page_draw(array(
            'title' => __('logbrowser'),
            'content' => view('admin/log_browse', array(
                'user_guid' => $user, 
                'timeupper' => $timeupper, 
                'timelower' => $timelower,
                'baseurl' => $_SERVER['REQUEST_URI'],
                'offset' => $offset,
                'count' => $count,
                'limit' => $limit,
                'entries' => $log
            ))
        ));

    }

    function action_add_user()
    {
        $this->validate_security_token();

        $username = get_input('username');
        $password = get_input('password');
        $password2 = get_input('password2');
        $email = get_input('email');
        $name = get_input('name');

        $admin = get_input('admin');
        if (is_array($admin)) $admin = $admin[0];

        if ($password != $password2)
        {
            redirect_back_error(__('create:passwords_differ'));
        }

        try
        {
            $new_user = register_user($username, $password, $name, $email);
            if ($admin != null)
            {
                $new_user->admin = true;
            }

            $new_user->admin_created = true;
            $new_user->created_by_guid = Session::get_loggedin_userid();
            $new_user->save();

            OutgoingMail::create(
                __('useradd:subject'),
                sprintf(__('useradd:body'), $name, Config::get('sitename'), Config::get('url'), $username, $password)
            )->send_to_user($new_user);                        

            SessionMessages::add(sprintf(__("adduser:ok"), Config::get('sitename')));
        }
        catch (ValidationException $r)
        {
            redirect_back_error($r->getMessage());
        }

        redirect_back();
    }
    
    function action_approve()
    {
        $this->validate_security_token();

        $guid = (int)get_input('org_guid');
        $org = Organization::get_by_guid($guid);

        if (!$org)
        {
            return $this->not_found();
        }
        
        $approvedBefore = $org->is_approved();

        $org->approval = (int)get_input('approval');

        $approvedAfter = $org->is_approved();

        $org->save();

        if (!$approvedBefore && $approvedAfter && $org->email)
        {
            OutgoingMail::create(
                __('email:orgapproved:subject', $org->language),
                view('emails/org_approved', array('org' => $org))
            )->send_to_user($org);
        }
        
        $org->send_relationship_emails();

        SessionMessages::add(__('approval:changed'));

        forward($org->get_url());
    }

    function action_delete_entity()
    {
        $this->validate_security_token();

        $guid = get_input('guid');
        $entity = Entity::get_by_guid($guid);

        if ($entity)
        {
            $entity->disable();
            $entity->save();
            SessionMessages::add(sprintf(__('entity:delete:success'), $guid));
        }
        else
            SessionMessages::add_error(sprintf(__('entity:delete:fail'), $guid));

        $next = get_input('next');
        if ($next)
        {
            forward($next);
        }
        else
        {
            redirect_back();
        }
    }
    
    function action_add_featured()
    {
        $username = get_input('username');
        $user = User::get_by_username($username);
        if (!$user)
        {
            return $this->not_found();
        }

        $this->page_draw(array(
            'title' => __('featured:add'),
            'content' => view('admin/add_featured', array('entity' => $user)),
        ));                
    }
    
    function action_activate_email()
    {
        $this->validate_security_token();
    
        $email = EmailTemplate::get_by_guid(get_input('email'));
        if (!$email)
        {
            return $this->not_found();
        }
        
        foreach (EmailTemplate::query()->where('active<>0')->filter() as $activeEmail)
        {
            $activeEmail->active = 0;
            $activeEmail->save();
        }

        $email->active = 1;            
        $email->save();
     
        SessionMessages::add('activated');
        forward('/admin/emails');        
    }    
    
    function action_activate_featured()
    {
        $this->validate_security_token();
        
        $guid = get_input('guid');
        $featuredSite = FeaturedSite::get_by_guid($guid);
        
        if (!$featuredSite)
        {
            return $this->not_found();
        }
        $activeSites = FeaturedSite::query()->where('active<>0')->filter();
        
        $featuredSite->active = 1;
        $featuredSite->save();
        
        foreach ($activeSites as $activeSite)
        {
            $activeSite->active = 0;
            $activeSite->save();
        }
        forward('org/featured');
    }
    
    function action_new_featured()
    {
        $this->validate_security_token();
    
        $username = get_input('username');
        $user = User::get_by_username($username);
        if (!$user)
        {
            return $this->not_found();
        }
        
        $featuredSite = new FeaturedSite();
        $featuredSite->container_guid = $user->guid;
        $featuredSite->image_url = get_input('image_url');
        $featuredSite->set_content(get_input('content'));
        $featuredSite->save();
        SessionMessages::add('featured:created');
        forward('org/featured');
    }
    
    function action_save_featured()
    {
        $this->validate_security_token();
    
        $featuredSite = FeaturedSite::get_by_guid(get_input('guid'));
        if (!$featuredSite)
        {
            return $this->not_found();
        }
        $featuredSite->image_url = get_input('image_url');
        $featuredSite->set_content(get_input('content'));
        $featuredSite->save();
        SessionMessages::add('featured:saved');
        forward('org/featured');
    }    
    
    function action_edit_featured()
    {
        $guid = get_input('guid');
        $featuredSite = FeaturedSite::get_by_guid($guid);
        if (!$featuredSite)
        {
            return $this->not_found();
        }
        
        $this->page_draw(array(
            'title' => __('featured:edit'),
            'content' => view('admin/edit_featured', array('entity' => $featuredSite)),
        ));
    }
   
    function action_add_email()
    {
        $this->page_draw(array(
            'title' => __('email:add'),
            'content' => view('admin/add_email'),
        ));                
    }
    
    function action_new_email()
    {
        $this->validate_security_token();
        
        $content = get_input('content');
        
        $email = new EmailTemplate();
        $email->from = get_input('from');
        $email->subject = get_input('subject');        
        $email->set_content($content);
        $email->save();
        forward("/admin/view_email?email={$email->guid}");
    }
    
    function action_save_email()
    {
        $this->validate_security_token();
        
        $email = EmailTemplate::get_by_guid(get_input('email'));
        if (!$email)
        {
            return $this->not_found();        
        }
        
        if (get_input('delete'))
        {
            $email->disable();
            $email->save();
            forward("/admin/emails");
        }
        else
        {
            $email->subject = get_input('subject');                
            $email->set_content(get_input('content'));
            $email->from = get_input('from');
            $email->save();
        }
        forward("/admin/view_email?email={$email->guid}");    
    }

    function action_delete_feed_item()
    {
        $this->validate_security_token();
        $feedItem = FeedItem::query()->where('id = ?', (int)get_input('item'))->get();
        
        if (!$feedItem)
        {
            return $this->not_found();
        }
        
        foreach ($feedItem->query_items_in_group()->filter() as $item)
        {
            $item->delete();
        }           
        SessionMessages::add("Feed item deleted successfully.");
        redirect_back();
    }
    
    function action_add_featured_photo()
    {
        $this->page_draw(array(
            'title' => __('featured_photo:add'),
            'content' => view('admin/add_featured_photo', array(
                'image_url' => get_input('image_url'),
                'href' => get_input('href'),
                'user_guid' => get_input('user_guid')
            )),
        ));        
    }
    
    function action_edit_featured_photo()
    {
        $photo = FeaturedPhoto::get_by_guid(get_input('guid'));        
        if (!$photo)
        {
            return $this->not_found();
        }
        
        $this->page_draw(array(
            'title' => __('featured_photo:edit'),
            'content' => view('admin/edit_featured_photo', array(
                'photo' => $photo,
            ))
        ));        
    }
    
    function action_new_featured_photo()
    {
        $this->validate_security_token();
        
        $featured_photo = new FeaturedPhoto();
        $featured_photo->user_guid = get_input('user_guid');
        $featured_photo->image_url = get_input('image_url');
        $featured_photo->x_offset = (int)get_input('x_offset');
        $featured_photo->y_offset = (int)get_input('y_offset');
        $featured_photo->weight = (double)get_input('weight');
        $featured_photo->href = get_input('href');
        $featured_photo->caption = get_input('caption');
        $featured_photo->org_name = get_input('org_name');
        $featured_photo->active = get_input('active') == 'yes' ? 1 : 0;
        $featured_photo->save();
        
        SessionMessages::add(__("featured_photo:added"));
        forward("/admin/featured_photos");
    }
    
    function action_save_featured_photo()
    {
        $this->validate_security_token();
        
        $featured_photo = FeaturedPhoto::get_by_guid(get_input('guid'));
        if (!$featured_photo)
        {
            return $this->not_found();
        }        
                
        $featured_photo->x_offset = (int)get_input('x_offset');
        $featured_photo->y_offset = (int)get_input('y_offset');
        $featured_photo->weight = (double)get_input('weight');
        $featured_photo->href = get_input('href');
        $featured_photo->caption = get_input('caption');
        $featured_photo->org_name = get_input('org_name');
        $featured_photo->active = get_input('active') == 'yes' ? 1 : 0;
        $featured_photo->save();
        
        SessionMessages::add(__("featured_photo:saved"));
        forward("/admin/featured_photos");    
    }
    
    function action_delete_featured_photo()
    {
        $this->validate_security_token();
        
        $photo = FeaturedPhoto::get_by_guid(get_input('guid'));
        if (!$photo)
        {
            return $this->not_found();
        }
        
        $photo->delete();
        
        SessionMessages::add(__("featured_photo:deleted"));
        forward("/admin/featured_photos");
    }
    
    function action_featured_photos()
    {
        $this->page_draw(array(
            'title' => __('featured_photo:all'),
            'content' =>  view('admin/featured_photos', array(
                'photos' => FeaturedPhoto::query()->filter()
            )),
            'theme_name' => 'editor_wide'
        ));
    }
}