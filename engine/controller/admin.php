<?php

class Controller_Admin extends Controller
{

    function before()
    {
        $this->require_admin();
        PageContext::set_theme('editor');

    }

    function action_contact()
    {
        $title = __('email:send');
        $area1 = view('admin/contact');
        $body = view_layout("one_column_wide", view_title($title), $area1);
        $this->page_draw($title,$body);
    }

    function action_emails()
    {
        $emails = EmailTemplate::query()->filter();
        $title = __('email:list');
        $area1 = view('admin/list_emails', array('emails' => $emails));
        $body = view_layout("one_column_padded", view_title($title), $area1);
        $this->page_draw($title,$body);
    }

    function action_view_email()
    {
        $title = __('email:view');
        $org = get_user_by_username(get_input('username'));
        
        PageContext::set_translatable(false);
               
        $email = get_entity(get_input('email')) ?: EmailTemplate::query()->where('active<>0')->get();

        if ($email && $email instanceof EmailTemplate)
        {
            $area1 = view('admin/view_email', array('org' => $org, 'email' => $email, 'from' => get_input('from')));

            $body = view_layout("one_column", view_title($title), $area1);

            $this->page_draw($title,$body);
        }
        else
        {
            not_found();
        }
    }        
    
    function action_edit_email()
    {
        $title = __('email:edit');
        $email = get_entity(get_input('email'));
        if ($email && $email instanceof EmailTemplate)
        {
            $area1 = view('admin/edit_email', array('email' => $email));
            $body = view_layout("one_column_padded", view_title($title), $area1);
            $this->page_draw($title,$body);
        }
        else
        {
            not_found();
        }        
    }
    
    function action_view_email_body()
    {
        $user = get_user_by_username(get_input('username'));
        $email = get_entity(get_input('email'));

        if ($email && $email instanceof EmailTemplate)
        {
            echo view('emails/template', array('org' => $user, 'base' => 'http://ERROR_RELATIVE_URL/ERROR_RELATIVE_URL/', 'email' => $email));            
        }
        else
        {
            not_found();
        }
    }
    
    function action_batch_email()
    {
        $email = get_entity(get_input('email')) ?: EmailTemplate::query()->where('active<>0')->get();
     
        $org_guids = get_input_array('orgs');
        if ($org_guids)
        {
            $orgs = Organization::query()->where_in('e.guid', $org_guids)->filter();
        }
        else
        {         
            $orgs = Organization::query()->
                where('approval > 0')->
                where("email <> ''")->
                where("((last_notify_time IS NULL) OR (last_notify_time + notify_days * 86400 < ?))", time())->
                where('notify_days > 0')->
                order_by('last_notify_time')->
                limit(20)->
                filter(); 
        }

        if ($email)
        {
            $title = __('email:batch');
            $body = view('admin/batch_email', array('email' => $email, 'orgs' => $orgs));
            $this->page_draw($title, view_layout("one_column_padded", view_title($title), $body));
        }
    }

    function action_send_batch_email()
    {
        $this->validate_security_token();
        
        $email = get_entity(get_input('email'));
        $org_guids = get_input_array('orgs');
        $numSent = 0;
        foreach ($org_guids as $org_guid)
        {       
            $org = get_entity($org_guid);

            if ($email->can_send_to($org))
            {
                $numSent++;
                $email->send_to($org);
            }
        }
        system_message("sent $numSent emails");
        forward(get_input('from') ?: "/admin/batch_email?email={$email->guid}");
    }
    
    
    function action_send_email()
    {
        $this->validate_security_token();
        
        $email = get_entity(get_input('email'));
        $org = get_entity(get_input('org_guid'));
        
        if ($email->can_send_to($org))
        {
            $email->send_to($org);
            system_message(__('email:reminder:sent'));
        }
        else
        {
            register_error(__('email:reminder:none'));
        }

        forward(get_input('from') ?: "/admin/contact");
    }

    function action_translateQueue()
    {
        $title = __('translate:queue');

        $body = view_layout("one_column_padded", view_title($title),
            view('translate/queue', array('lang' => get_language()))
        );

        $this->page_draw($title,$body);
    }


    function action_statistics()
    {
        $title = __("admin:statistics");
        $this->page_draw($title,
            view_layout("one_column_padded", view_title($title), view("admin/statistics")));
    }

    function action_user()
    {
        $search = get_input('s');
        $limit = get_input('limit', 10);
        $offset = get_input('offset', 0);

        $title = view_title(__('admin:user'));
        
        $count = User::query()->count();
        $entities = User::query()->limit($limit, $offset)->order_by('e.guid desc')->filter();

        $result = view_entity_list($entities, $count, $offset, $limit);

        $this->page_draw(
            __("admin:user"),
            view_layout("one_column_padded", $title,  view("admin/user") . $result)
        );
    }

    function action_search()
    {
        $tag = stripslashes(get_input('tag'));
        $title = sprintf(__('search:title_with_query'),$tag);

        if (!empty($tag)) 
        {
            $body = view_layout('one_column_padded',view_title($title),$this->search_users($tag));
        }

        $this->page_draw($title,$body);

    }

    function search_users($tag)
    {
        $limit = 10;
        $offset = (int)get_input('offset');

        $object = get_input('object');
       
        $query = User::query()->where('(INSTR(u.username, ?) > 0 OR INSTR(u.name, ?) > 0)', $tag, $tag);
       
        if ($users = $query->limit($limit, $offset)->filter())         
        {
            $count = $query->count();
            $return = view('user/search/startblurb',array('count' => $count, 'tag' => $tag));            
            $return .= view_entity_list($users, $count, $offset, $limit);
            return $return;
        }
    }
    
    function action_logbrowser()
    {
        $query = SystemLog::query();
    
        $limit = get_input('limit', 40);
        $offset = get_input('offset');

        $search_username = get_input('search_username');
        if ($search_username) {
            if ($user = get_user_by_username($search_username)) {
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

        $title = view_title(__('logbrowser'));
        
        if ($user)
        {
            $query->where('performed_by_guid=?', $user);
        }
                
        $query->limit($limit, $offset);
        
        $log = $query->filter();
        $count = $query->count();

        $form = view('logbrowser/form', array(
            'user_guid' => $user, 
            'timeupper' => $timeupper, 
            'timelower' => $timelower,
            'baseurl' => $_SERVER['REQUEST_URI'],
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
            'entries' => $log
        ));

        $this->page_draw(__('logbrowser'),view_layout("one_column_padded", $title,  $form));

    }

    function action_add_user()
    {
        $this->validate_security_token();

        global $CONFIG;

        $username = get_input('username');
        $password = get_input('password');
        $password2 = get_input('password2');
        $email = get_input('email');
        $name = get_input('name');

        $admin = get_input('admin');
        if (is_array($admin)) $admin = $admin[0];

        if ($password != $password2)
        {
            action_error(__('create:passwords_differ'));
        }

        try
        {
            $new_user = register_user($username, $password, $name, $email, true);
            if ($admin != null)
            {
                $new_user->admin = true;
            }

            $new_user->admin_created = true;
            $new_user->created_by_guid = Session::get_loggedin_userid();
            $new_user->save();

            $new_user->notify(
                __('useradd:subject'),
                sprintf(__('useradd:body'), $name, $CONFIG->sitename, $CONFIG->url, $username, $password)
            );

            system_message(sprintf(__("adduser:ok"),$CONFIG->sitename));
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }

        forward_to_referrer();
    }

    function action_approve()
    {
        $this->validate_security_token();

        $guid = (int)get_input('org_guid');
        $entity = get_entity($guid);

        global $CONFIG;
        
        if (($entity) && ($entity instanceof Organization))
        {
            $approvedBefore = $entity->isApproved();

            $entity->approval = (int)get_input('approval');

            $approvedAfter = $entity->isApproved();

            $entity->save();

            if (!$approvedBefore && $approvedAfter && $entity->email)
            {
                $entity->notify(
                    __('email:orgapproved:subject', $entity->language),
                    sprintf(__('email:orgapproved:body', $entity->language),
                        $entity->name,
                        $entity->getURL(),
                        "{$CONFIG->url}pg/login?username={$entity->username}",
                        __('help:title', $entity->language),
                        "{$entity->getURL()}/help"
                    )
                );
            }

            system_message(__('approval:changed'));
        }
        else
        {
            register_error(__('approval:notapproved'));
        }

        forward($entity->getUrl());

    }

    function action_delete_entity()
    {
        $this->validate_security_token();

        $guid = get_input('guid');
        $entity = get_entity($guid);

        if ($entity)
        {
            if ($entity->delete())
                system_message(sprintf(__('entity:delete:success'), $guid));
            else
                register_error(sprintf(__('entity:delete:fail'), $guid));
        }
        else
            register_error(sprintf(__('entity:delete:fail'), $guid));

        $next = get_input('next');
        if ($next)
        {
            forward($next);
        }
        else
        {
            forward_to_referrer();
        }
    }
    
    function action_add_featured()
    {
        $username = get_input('username');
        $user = get_user_by_username($username);
        if ($user)
        {
            $title = __('featured:add');
            $body = view('admin/add_featured', array('entity' => $user));
            $this->page_draw($title, view_layout("one_column_padded", 
                view_title($title), $body));        
        }
        else
        {
            not_found();
        }
    }
    
    function action_activate_email()
    {
        $this->validate_security_token();
    
        $email = get_entity(get_input('email'));
        if ($email && $email instanceof EmailTemplate)
        {
            foreach (EmailTemplate::query()->where('active<>0')->filter() as $activeEmail)
            {
                $activeEmail->active = 0;
                $activeEmail->save();
            }

            $email->active = 1;            
            $email->save();
         
            system_message('activated');
            forward('/admin/emails');        
        }
        else
        {
            not_found();
        }
    }    
    
    function action_activate_featured()
    {
        $this->validate_security_token();
        
        $guid = get_input('guid');
        $entity = get_entity($guid);
        
        if ($entity && $entity instanceof FeaturedSite)
        {
            $activeSites = FeaturedSite::query()->where('active<>0')->filter();
            
            $entity->active = 1;
            $entity->save();
            
            foreach ($activeSites as $activeSite)
            {
                $activeSite->active = 0;
                $activeSite->save();
            }
            forward('org/featured');
        }
        else        
        {   
            not_found();
        }

    }
    
    function action_new_featured()
    {
        $this->validate_security_token();
    
        $username = get_input('username');
        $user = get_user_by_username($username);
        if ($user)
        {
            $featuredSite = new FeaturedSite();
            $featuredSite->container_guid = $user->guid;
            $featuredSite->image_url = get_input('image_url');
            $featuredSite->setContent(get_input('content'), true);
            $featuredSite->save();
            system_message('featured:created');
            forward('org/featured');
        }
        else
        {
            not_found();
        }
    }
    
    function action_save_featured()
    {
        $this->validate_security_token();
    
        $featuredSite = get_entity(get_input('guid'));
        if ($featuredSite && $featuredSite instanceof FeaturedSite)
        {
            $featuredSite->image_url = get_input('image_url');
            $featuredSite->setContent(get_input('content'), true);
            $featuredSite->save();
            system_message('featured:saved');
            forward('org/featured');
        }
        else
        {
            not_found();
        }
    }    
    
    function action_edit_featured()
    {
        $guid = get_input('guid');
        $featuredSite = get_entity($guid);
        if ($featuredSite && $featuredSite instanceof FeaturedSite)
        {
            $title = __('featured:edit');
            $body = view('admin/edit_featured', array('entity' => $featuredSite));
            $this->page_draw($title, view_layout("one_column_padded", 
                view_title($title), $body));        
        }
        else
        {
            not_found();
        }
    }
   
    function action_add_email()
    {
        $title = __('email:add');
        $body = view('admin/add_email');
        $this->page_draw($title, view_layout("one_column_padded", view_title($title), $body));  
    }
    
    function action_new_email()
    {
        $this->validate_security_token();
        
        $content = get_input('content');
        
        $email = new EmailTemplate();
        $email->from = get_input('from');
        $email->subject = get_input('subject');        
        $email->setContent($content, true);
        $email->save();
        forward("/admin/view_email?email={$email->guid}");
    }
    
    function action_save_email()
    {
        $this->validate_security_token();
        
        $email = get_entity(get_input('email'));
        if ($email && $email instanceof EmailTemplate)
        {
            if (get_input('delete'))
            {
                $email->disable();
                $email->save();
                forward("/admin/emails");
            }
            else
            {
                $email->subject = get_input('subject');                
                $email->setContent(get_input('content'), true);
                $email->from = get_input('from');
                $email->save();
            }
            forward("/admin/view_email?email={$email->guid}");    
        }
        else
        {
            not_found();
        }
    }

}