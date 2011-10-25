<?php

/*
 * Controller for actions that are only accessible to site administrators.
 *
 * URL: /admin/<action>
 */
class Controller_Admin extends Controller
{
    static $routes; // initialized at bottom of file

    function before()
    {
        $this->require_admin();
        $this->page_draw_vars['theme_name'] = 'editor';
    }
    
    function action_resend_mail()
    {
        $action = new Action_Admin_ResendMail($this);
        $action->execute();        
    }
    
    function action_resend_sms()
    {
        $action = new Action_Admin_ResendSMS($this);
        $action->execute();        
    }    
    
    function action_set_mail_status()
    {
        $action = new Action_Admin_SetMailStatus($this);
        $action->execute();        
    }    
    
    function action_subscriptions()
    {
        $action = new Action_Admin_Subscriptions($this);
        $action->execute();
    }
    
    function action_view_mail()
    {
        $id = (int)get_input('id');
        $mail = OutgoingMail::query()->where('id = ?', $id)->get();
        if (!$mail)
        {
            throw new NotFoundException();
        }
        
        $this->page_draw(array(
            'title' => __('email:view'),
            'content' => view('admin/view_mail', array('mail' => $mail))
        ));
    }
    
    function action_view_sms()
    {
        $id = (int)get_input('id');
        $sms = OutgoingSMS::query()->where('id = ?', $id)->get();
        if (!$sms)
        {
            throw new NotFoundException();
        }
        
        $this->page_draw(array(
            'title' => __('sms:view'),
            'content' => view('admin/view_sms', array('sms' => $sms))
        ));
    }    
    
    function action_recent_photos()
    {
        $this->page_draw(array(
            'title' => 'Recent Photos',
            'content' => view('admin/recent_content', array('content_filter' => "%<img%")),
            'theme_name' => 'editor',
        ));             
    }

    function action_recent_documents()
    {
        $this->page_draw(array(
            'title' => 'Recent Documents',
            'content' => view('admin/recent_content', array('content_filter' => "%<scribd%")),
            'theme_name' => 'editor',
        ));             
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
    
    function action_outgoing_sms()
    {
        $this->page_draw(array(
            'title' => __('sms:outgoing_sms'),
            'content' => view('admin/outgoing_sms'),
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

        $this->page_draw(array(
            'title' => __('logbrowser'),
            'content' => view('admin/log_browse', array(
                'user_guid' => $user, 
                'timeupper' => $timeupper, 
                'timelower' => $timelower,
                'baseurl' => $_SERVER['REQUEST_URI'],
                'offset' => $offset,
                'count' => null,
                'count_displayed' => sizeof($log),
                'limit' => $limit,
                'entries' => $log
            ))
        ));

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
}

Controller_Admin::$routes = Controller::$SIMPLE_ROUTES;