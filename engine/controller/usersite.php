<?php

/*
 * Controller for the top level of a user's site.
 *
 * widget_name may refer to an actual widget name for pre-defined widgets
 * (an alias for /<username>/page/<widget_name>[/<action>])
 *
 * URL: /<username>[/<widget_name>[/<action>]]
 */
class Controller_UserSite extends Controller_User
{
    static $routes = array(
        array(
            'regex' => '(/)?$',
            'action' => 'action_index',
        ),
        array(
            'regex' => '/(?P<controller>post|page|topic|widget)\b',
        ),        
        array(
            'regex' => '/(?P<action>\w+)\b',
        ),
        array(
            'regex' => '/(?P<widget_name>[\w\-]+)(/(?P<action>\w+))?',
            'defaults' => array('action' => 'view'),
            'action' => 'action_widget_<action>',
        )
    );

    function action_index()
    {
        $org = $this->get_org();
        
        if (!$org)
        {
            return $this->action_settings();
        }
        else
        {                             
            $this->page_draw_vars['is_site_home'] = true;
            
            $home_widget = $org->query_menu_widgets()->get();             
            return $this->index_widget($home_widget);
        }
    }
    
    function action_widget_view()
    {
        $org = $this->get_org();
        $widgetName = $this->param('widget_name');
        
        if ($org)
        {            
            $widget = $org->get_widget_by_name($widgetName);                        

            $home_widget = $org->query_menu_widgets()->get();
            if ($home_widget && $widget && $home_widget->guid == $widget->guid)
            {                
                $this->page_draw_vars['is_site_home'] = true;
            }
            
            return $this->index_widget($widget);
        }                   
        else
        {        
            throw new NotFoundException();
        }
    }
        
    function action_widget_edit()
    {    
        // backwards compatibility to avoid breaking links and allow editing widgets
        // at /<username>/<widget_name>/edit         
        // by forwarding to new URLs at /<username>/page/<widget_name>/edit         
     
        $org = $this->get_org();
        if (!$org)
        {
            throw new NotFoundException();
        }
        
        $widgetName = $this->param('widget_name');
        $widget = $org->get_widget_by_name($widgetName);
        
        if (!$widget->is_enabled())
        {
            throw new NotFoundException();            
        }
        $this->redirect($widget->get_edit_url());
    }
    
    function action_add_page()
    {
        $action = new Action_AddWidget($this, $this->get_org());
        $action->execute();
    }
        
    function action_design()
    {
        $action = new Action_EditDesign($this);
        $action->execute();            
    }
    
    function action_help()
    {
        $this->require_editor();
        $this->require_org();
        
        $this->page_draw(array(
            'title' => __("help:title"),
            'content' => view("org/help", array('org' => $this->get_org())),           
        ));        
    }

    function action_dashboard()
    {    
        $this->require_editor();        
        $this->allow_view_types(null);        

        $user = $this->get_user();
        if ($user->guid == Session::get_loggedin_userid())
        {
            $title = __('edit_site');
        }
        else
        {
            $title = sprintf(__('edit_item'), $user->name);
        }
                
        $org = $this->get_org();
        if ($org)
        {            
            $content = view("org/dashboard", array('org' => $org));
            $pre_body = view("org/todo_message", array('org' => $org));                 
        }
        else if ($user->admin)
        {
            $content = view('admin/dashboard');
            $pre_body = '';
        }
        else
        {
            throw new RedirectException('', $user->get_url());
        }
        
        $this->page_draw(array(
            'title' => $title,
            'content' => $content,
            'pre_body' => $pre_body
        ));
    }
    
    function action_password()
    {
        $action = new Action_ChangePassword($this);
        $action->execute();    
    }

    function action_username()
    {
        $action = new Action_ChangeUsername($this);
        $action->execute();
    }

    function action_settings()
    {    
        $action = new Action_Settings($this);
        $action->execute();
    }

    function action_addphotos()
    {
        $action = new Action_AddPhotos($this);
        $action->execute();        
    }
            
    function action_send_message()
    {
        $action = new Action_SendMessage($this);
        $action->execute();   
    }
    
    function action_domains()
    {
        $this->require_org();
        $this->require_admin();
        $this->use_editor_layout();
        
        $this->page_draw(array(
            'title' => __('domains:edit'),
            'content' => view('org/domains', array('org' => $this->get_org())),
        ));
    }
    
    function action_add_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $domain_name = get_input('domain_name');
        if (OrgDomainName::query()->where('domain_name = ?', $domain_name)->exists())
        {
            throw new RedirectException(__('domains:duplicate'));
        }
        if (preg_match('/[^\w\.\-]/', $domain_name))
        {
            throw new RedirectException(__('domains:invalid'));
        }
        
        $org_domain_name = new OrgDomainName();
        $org_domain_name->domain_name = $domain_name;
        $org_domain_name->guid = $this->get_org()->guid;
        $org_domain_name->save();
        SessionMessages::add(__('domains:added'));
        $this->redirect();
    }
    
    function action_delete_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $org_domain_name = OrgDomainName::query()->where('id = ?', (int)get_input('id'))->get();
        if (!$org_domain_name)
        {
            throw new RedirectException(__('domains:not_found'));
        }
        $org_domain_name->delete();
        SessionMessages::add(__('domains:deleted'));
        $this->redirect();
    }

    function action_share()
    {
        $action = new Action_Share($this);
        $action->execute();
    }
    
    function action_relationship_emails_js()
    {    
        $this->require_editor();
        $this->require_org();
    
        $this->set_content_type('text/javascript');
        
        $org = $this->get_org();
                
        $relationships = $org->query_relationships()
            ->where("subject_guid <> 0 OR subject_email <> ''")
            ->filter();
     
        $emails = array();
        
        foreach ($relationships as $relationship)
        {
            $email = $relationship->get_subject_email();
            if ($email)
            {        
                $emails[] = $email;
            }
        }
     
        $this->set_content(json_encode(array('emails' => $emails)));
    }    
}