<?php

/*
 * Controller for actions that are only accessible to site administrators.
 *
 * URL: /admin/<action>
 */
class Controller_Admin extends Controller_Simple
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
            'title' => __('user:contact_list'),
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
        $action = new Action_Admin_ResendMail($this);
        $action->execute();        
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
            $content = __('search:noresults');
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
        $action = new Action_Admin_AddUser($this);
        $action->execute();
    }
    
    function action_approve()
    {
        $action = new Action_Admin_ChangeOrgApproval($this);
        $action->execute();
    }

    function action_delete_entity()
    {
        $action = new Action_Admin_DeleteEntity($this);
        $action->execute();
    }
        
    function action_activate_featured()
    {
        $action = new Action_Admin_ActivateFeaturedSite($this);
        $action->execute();
    }
    
    function action_add_featured()
    {
        $action = new Action_Admin_AddFeaturedSite($this);
        $action->execute();
    }       
        
    function action_edit_featured()
    {
        $action = new Action_Admin_EditFeaturedSite($this);
        $action->execute();    
    }
   
    function action_edit_email()
    {
        $action = new Action_Admin_EditEmailTemplate($this);
        $action->execute();    
    }
   
    function action_add_email()
    {
        $action = new Action_Admin_AddEmailTemplate($this);
        $action->execute();               
    }

    function action_send_email()
    {
        $action = new Action_Admin_SendEmailTemplate($this);
        $action->execute();
    }
    
    function action_activate_email()
    {
        $action = new Action_Admin_ActivateEmailTemplate($this);
        $action->execute();    
    }
    
    function action_add_featured_photo()
    {
        $action = new Action_Admin_AddFeaturedPhoto($this);
        $action->execute();
    }
    
    function action_edit_featured_photo()
    {
        $action = new Action_Admin_EditFeaturedPhoto($this);
        $action->execute();  
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
    
    function action_translate()
    {
        $action = new Action_Admin_TranslateContent($this);
        $action->execute();
    }
}