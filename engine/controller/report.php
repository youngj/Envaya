<?php

class Controller_Report extends Controller_Profile
{
    protected $report;

    function before()
    {
        parent::before();

        $reportId = $this->request->param('id');

        if ($reportId == 'new')
        {
            $this->request->action = 'new';
            return;
        }

        $report = $this->org->query_reports()->where('u.guid = ?', $reportId)->get();
        if ($report)
        {
            $this->report = $report;
            return;
        }
        else
        {
            $this->use_public_layout();
            $this->org_page_not_found();
        }
    }

    function show_next_steps()
    {
        return false;
    }
    
    function action_index()
    {
        $org = $this->org;
        $report = $this->report;

        $this->use_public_layout();

        if ($report->can_edit())
        {
            PageContext::add_submenu_item(__("report:edit"), "{$report->get_url()}/edit", 'edit');
        }
        else if ($report->can_edit_access_settings())
        {
            PageContext::add_submenu_item(__("report:edit"), "{$report->get_url()}/access_settings", 'edit');
        }

        $title = $report->get_title();

        if (!$org->can_view())
        {
            $this->show_cant_view_message();
            $body = '';
        }
        else
        {
            $body = $this->org_view_body($title, view('reports/view', array('report' => $report)));
        }

        $this->page_draw($title,$body);
    }

    function use_editor_layout()
    {
        PageContext::set_theme('editor_wide');
    }    
    
    function require_editor()
    {
        parent::require_editor();

        $report = $this->report;

        if (!$report)
        {
            not_found();
        }
        else if (!$report->can_edit())
        {
            register_error(__('report:cantedit'));
            
            if ($report->status == ReportStatus::Submitted)
            {
                forward($report->get_url()."/submit_success");
            }
            else
            {
                forward($report->get_url());
            }
        }
    }

    function require_manager()
    {
        $report = $this->report;
        if (!$report->can_manage())
        {
            register_error(__('report:cantmanage'));
            force_login();
        }
        $this->use_editor_layout();
    }
    
    function action_edit()
    {
        $this->require_editor();
        $report = $this->report;
        
        $title = sprintf(__('report:edit_title'), $report->get_title());

        $cancelUrl = get_input('from') ?: $this->org->get_widget_by_name('reports')->get_edit_url();

        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $area1 = view('reports/edit', array(
            'report' => $report, 
            'start' => get_input('start'), 
            'scroll_position' => (int)get_input('scroll')
        ));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }
    
    function action_preview()
    {
        $report = $this->report;
        $this->require_editor();
        
        PageContext::add_submenu_item(__("report:cancel_preview"), $this->org->get_widget_by_name('reports')->get_edit_url(), 'edit');        
        
        $title = sprintf(__('report:preview'), $report->get_title());        
        $area1 = view('reports/preview', array('report' => $report));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }    
    
    function action_view_response()
    {
        $this->require_manager();
        $report = $this->report;        
        $report_def = $report->get_report_definition();
        
        PageContext::add_submenu_item(__("report:cancel_preview"), $report_def->get_url()."/edit?tab=manage", 'edit');        
        
        $title = sprintf(__('report:view_response_title'), $report->get_title());
        $area1 = view('reports/view_response', array('report' => $report));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body); 
    }
    
    function action_set_status()
    {
        $this->require_manager();
        $newStatus = (int)get_input('status');
        $report = $this->report;
        $report->status = $newStatus;
        $report->save();
        
        if ($report->status == ReportStatus::Approved)
        {
            $report->post_feed_items();
        }
        
        system_message(__('report:status_changed'));
        forward($report->get_report_definition()->get_url()."/edit?tab=manage");
    }
    
    function action_confirm_submit()
    {
        $this->require_editor();
        $report = $this->report;
        
        PageContext::add_submenu_item(__("report:cancel_submit"), $this->org->get_widget_by_name('reports')->get_edit_url(), 'edit');        
        
        $title = sprintf(__('report:confirm_submit'), $report->get_title());        
        $area1 = view('reports/preview', array('report' => $report, 'submit' => true));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }

    function action_submit()
    {
        $this->require_editor();
        $this->validate_security_token();
        $report = $this->report;
        
        $sent_email = false;
        
        if ($report->status != ReportStatus::Submitted)
        {        
            $report->signature = get_input('signature');
            $report->status = ReportStatus::Submitted;
            $report->time_submitted = time();
            $report->save();           
            
            $report_def = $report->get_report_definition();
            $report_recipient = $report_def->get_container_entity();
            if ($report_recipient && $report_recipient->email)
            {
                $email_body = view('emails/report_submitted', array('report' => $report));
                $email_subject = sprintf(__('report:submitted_subject', $report_recipient->language), 
                    $report->get_title(), $report->get_container_entity()->name);
                
                $headers = array(
                    'To' => $report_recipient->get_name_for_email(),
                    'Content-Type' => 'text/html',
                );
                
                send_mail($report_recipient->email, $email_subject, $email_body, $headers);
                send_mail(Config::get('admin_email'), $email_subject, $email_body, $headers);
            }
            
            $report_org = $report->get_container_entity();
            if ($report_org && $report_org->email)
            {
                $email_body = view('emails/report_submit_success', array('report' => $report));
                $email_subject = sprintf(__('report:submit_success_subject', $report_org->language), 
                    $report->get_title(), $report_recipient->name);
                    
                $headers = array(
                    'To' => $report_org->get_name_for_email(),
                    'Content-Type' => 'text/html',
                );
                
                send_mail($report_org->email, $email_subject, $email_body, $headers);                    
                $sent_email = true;
            }
        }
        
        forward($report->get_url()."/submit_success" . ($sent_email ? "?sent_email=1" : ""));
    }
    
    function action_submit_success()
    {
        $this->use_editor_layout();
        $report = $this->report;
    
        if ($report->status >= ReportStatus::Submitted)
        {                
            $title = sprintf(__('report:submit_success'), $report->get_title());        
            $area1 = view('reports/submit_success', array('report' => $report, 'sent_email' => get_input('sent_email')));
            $body = view_layout("one_column", view_title($title), $area1);
            $this->page_draw($title,$body);  
        }
        else
        {
            register_error(__('report:not_submitted'));
            forward($report->get_edit_url());
        }
    }

    function action_save()
    {
        $this->require_editor();
        $this->validate_security_token();
        $report = $this->report;

        $field_names = get_input_array('fields');
        
        foreach ($field_names as $field_name)
        {        
            $field = $report->get_field($field_name);            
            $field->value = $field->get_input_value();            
        }
        
        if ($report->status == ReportStatus::Blank)
        {
            $report->status = ReportStatus::Draft;        
        }
        $user_save_time = (int)get_input('user_save_time');        
        if ($user_save_time)
        {
            $report->user_save_time = $user_save_time;
        }        
        
        $report->save();
        
        $next_section = get_input('next_section');        
        if ($next_section)
        {           
            $url = $report->get_edit_url()."?section=$next_section&saved=1";
            $scroll = (int)get_input('scroll_position');
            if ($scroll)
            {
                $url .= "&scroll=$scroll";
            }        
            forward($url);
        }
        else
        {
            $org = $report->get_container_entity();            
            forward($report->get_url()."/confirm_submit");
        }
    }

    function action_new()
    {
        $this->require_editor();
        $this->validate_security_token();
    }
    
    private function save_access_settings()
    {
        $report = $this->report;
        $field_names = get_input_array('fields');
        
        foreach ($field_names as $field_name)
        {
            $field = $report->get_field($field_name);

            $new_access = (int)get_input($field_name);
            if ($new_access != $field->access)
            {
                $field->access = $new_access;
                $field->save();
            }
        }
        system_message(__('report:access_settings_saved'));
        forward($report->get_url());    
    }    
    
    
    
    function action_access_settings()
    {    
        $this->use_editor_layout();
                
        $report = $this->report;
        
        if ($report->can_edit_access_settings())
        {        
            if (Request::is_post())
            {
                $this->save_access_settings();
            }
        
            PageContext::add_submenu_item(__("canceledit"), $this->org->get_widget_by_name('reports')->get_edit_url(), 'edit');        
        
            $title = sprintf(__('report:access_settings'), $report->get_title());
            
            $area1 = view('reports/access_settings', 
                array('report' => $report)
            );
            
            $body = view_layout("one_column", view_title($title), $area1);
            $this->page_draw($title,$body);  
        }
        else
        {
            register_error(__('report:cantedit'));
            forward($report->get_url());
        }
    }
}