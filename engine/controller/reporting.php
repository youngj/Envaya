<?php

class Controller_Reporting extends Controller_Profile
{
    protected $report_def;

    function before()
    {   
        parent::before();

        $reportId = $this->request->param('id');

        if ($reportId == 'new' || $reportId == 'add')
        {
            $this->request->action = $reportId;
            return;
        }

        $report_def = $this->org->query_report_definitions()->where('u.guid = ?', $reportId)->get();
        if ($report_def)
        {
            $this->report_def = $report_def;
            return;
        }
        else
        {
            $this->use_public_layout();
            $this->org_page_not_found();
        }
    }

    function use_editor_layout()
    {
        PageContext::set_theme('editor_wide');
    }        
    
    function show_next_steps()
    {
        return false;
    }
    
    function action_index()
    {
        $org = $this->org;
        $report_def = $this->report_def;

        $this->use_public_layout();

        if ($report_def->can_edit())
        {
            add_submenu_item(__("report:edit"), "{$report_def->get_url()}/edit", 'edit');
        }

        $title = $report_def->get_title();
        $body = $this->org_view_body($title, view('reports/view_definition', array('report_def' => $report_def)));

        $this->page_draw($title,$body);
    }
    
    function action_edit()
    {
        $this->require_editor();        
        
        $report_def = $this->report_def;

        $title = $report_def->get_title();

        $cancelUrl = get_input('from') ?: $this->org->get_widget_by_name('reports')->get_edit_url();

        add_submenu_item(__("canceledit"), $cancelUrl, 'edit');
        
        $area1 = view('reports/edit_definition', array('report_def' => $report_def, 'tab' => get_input('tab')));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }    
    
    function action_add()
    {
        $this->require_editor();
        
        $title = __('report:add_new');
        
        $area1 = view('reports/add_definition', array('org' => $this->org));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);        
    }
    
    function action_new()
    {
        $this->require_editor();
        $this->validate_security_token();
        
        $report_def = new ReportDefinition();
        $report_def->name = get_input('report_name');        
        $report_def->handler_class = get_input('handler_class');
        $report_def->container_guid = $this->org->guid;
        $report_def->save();
        
        system_message(__('report:created'));
        
        forward("{$report_def->get_url()}/edit?tab=invite");
    }
    
    function action_delete()
    {
        $this->require_editor();
        $this->validate_security_token();
        $report_def = $this->report_def;
        $report_def->disable();
        $report_def->save();
        
        system_message(__('report:deleted'));
        $org = $this->org;
        
        forward($org->get_widget_by_name('reports')->get_edit_url());
    }
    
    function action_save()
    {
        $this->require_editor();
        $this->validate_security_token();
        $report_def = $this->report_def;        
        forward($report_def->get_url(). "/edit?section=".urlencode(get_input('next_section')));    
    }
    
    function action_start()
    {
        $report_def = $this->report_def;
        
        $this->use_public_layout(false);
        PageContext::set_translatable(false);
        
        $title = $report_def->get_title();
        
        $area1 = view('reports/start', array('report_def' => $report_def));        
        
        $body = $this->org_view_body($title, $area1);
        
        $this->page_draw($title,$body);        
    }
    
    function action_new_report()
    {
        $report_def = $this->report_def;
        $user = Session::get_loggedin_user();
        if ($user instanceof Organization)
        {
            $report = $user->query_reports()->where('report_guid = ?', $report_def->guid)->get();
            if (!$report)
            {
                $report = new Report();
                $report->report_guid = $report_def->guid;
                $report->container_guid = $user->guid;
                $report->save();
            }
            forward($report->get_edit_url()."?start=1");
        }
        else
        {
            register_error(__('report:invaliduser'));
            forward($this->report_def->get_url()."/create_account");
        }
    }
    
    function action_create_account()
    {
        if (get_input('logout'))
        {
            logout();
            forward($this->report_def->get_url(). "/create_account");
        }
    
        $loggedInUser = Session::get_loggedin_user();
        if ($loggedInUser)
        {
            if ($loggedInUser instanceof Organization)
            {
                if ($loggedInUser->is_setup_complete())
                {
                    return $this->action_new_report();                
                }
                else
                {
                    forward($this->report_def->get_url(). "/create_profile");
                }                
            }
            else
            {
                logout();
                forward($this->report_def->get_url(). "/create_account");
            }
        }
        
        $title = __("register:title");
        $body = view_layout('one_column', 
            view_title($title, array('org_only' => true)), 
            view("reports/create_account", array('report_def' => $this->report_def)));
        $this->page_draw($title, $body);    
    }
    
    function action_new_account()
    {
        try
        {            
            $org = $this->process_create_account_form();
            
            $org->country = 'tz'; // todo: add this to the form
            $org->save();

            forward($this->report_def->get_url()."/create_profile");
        }
        catch (PossibleDuplicateException $p)
        {
            $ts = time();
            $token = generate_security_token($ts);    
            $post_login_url = $this->report_def->get_url()."/new_report?__ts=$ts&__token=$token";
            
            $this->show_possible_duplicate($p, '/pg/login?next='.urlencode($post_login_url));
        }
        catch (RegistrationException $r)
        {
            register_error($r->getMessage());
            Session::save_input();
            forward("{$this->report_def->get_url()}/create_account");
        }        
    }
    
    function action_create_profile()
    {
        $loggedInUser = Session::get_loggedin_user();
        if (!$loggedInUser || !$loggedInUser instanceof Organization)
        {
            logout();
            forward($this->report_def->get_url(). "/create_account");
        }            

        $title = __("report:create_profile");
        $body = view_layout('one_column', 
            view_title($title, array('org_only' => true)), 
            view("reports/create_profile", array('report_def' => $this->report_def)));
            
        $this->page_draw($title, $body);    
        
    }
    
    function action_new_profile()
    {
        $this->require_login();
        $this->validate_security_token();

        try
        {
            $org = $this->process_create_profile_form();
            $this->action_new_report();
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }            
    }
}