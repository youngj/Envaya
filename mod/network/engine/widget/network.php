<?php

/* 
 * A widget that shows an organization's partner organizations (Relationships)
 * and allows the organization to add/remove partner organizations.
 */
class Widget_Network extends Widget
{
    static $default_menu_order = 60;
    static $default_widget_name = 'network';    

    function get_default_title()
    {
        return __('network:title');
    }

    function render_view($args = null)
    {
        return view("widgets/network_view", array('widget' => $this));
    }   

    function render_edit()
    {                
        switch (get_input('action'))       
        {
            case 'add_relationship':        return $this->add_relationship_view();                               
            case 'edit_relationship':       return $this->edit_relationship_view();                                
            
            default:                 
                return view("widgets/network_edit", array('widget' => $this));
        }    
    }

    function process_input($action)
    {        
        switch (get_input('action'))
        {
            case 'delete_relationship':  return $this->delete_relationship($action);
            case 'edit_relationship':    return $this->save_relationship($action);
            case 'add_relationship':     return $this->add_relationship($action);
            
            default: 
                $this->save();
                return;
        }
    }
    
    private function show_cancel_edit_button()
    {
        $submenu = PageContext::get_submenu('edit');
        $submenu->clear();
        $submenu->add_link(__("cancel"), $this->get_edit_url());
    }
    
    private function add_relationship_view()
    {
        $this->show_cancel_edit_button();
                        
        $org_guid = (int)get_input('org_guid');
        $org = Organization::get_by_guid($org_guid);
        
        $type = (int)get_input('type');
        if (!Relationship::is_valid_type($type))
        {
            throw new NotFoundException();
        }
                
        return view('widgets/network_add_relationship', array(
            'widget' => $this, 
            'org' => $org,
            'type' => $type
        ));            
    }    
    
    private function duplicate_error($relationship)
    {
        throw new RedirectException(strtr(__('network:duplicate'), array(
                '{name}' => $relationship->get_subject_name(),
                '{type}' => $relationship->msg('header')
        )));
    }
    
    private function add_relationship($action)
    {
        $org = $this->get_container_entity();
        
        $reverse = null;
    
        $relationship = new Relationship();
        $relationship->type = (int)get_input('type');
        $relationship->container_guid = $org->guid;
        $relationship->set_self_approved();
        
        $relationship->set_content(get_input('content'));

        if (!Relationship::is_valid_type($relationship->type))
        {
            throw new NotFoundException();
        }
        
        $relationship->subject_phone = get_input('phone_number');
        $relationship->subject_website = $this->clean_url(get_input('website'));        
        
        $subject_org = Organization::get_by_guid(get_input('org_guid'));        
        if (!$subject_org) // subject_org not an envaya member
        {
            $relationship->subject_name = get_input('name');
            $relationship->subject_guid = 0;
            $relationship->invite_subject = get_input('invite') ? true : false;
            $relationship->subject_email = EmailAddress::validate(trim(get_input('email')));
            
            $matchingRelationships = Relationship::query_for_user($org)
                ->where('`type` = ?', $relationship->type)
                ->where('subject_name = ?', $relationship->subject_name)
                ->where('subject_email = ?', $relationship->subject_email)
                ->where('subject_website = ?', $relationship->subject_website)
                ->count();

            if ($matchingRelationships > 0)
            {
                return $this->duplicate_error($relationship);
            }
        }
        else // subject_org already an envaya member
        {            
            $relationship->subject_guid = $subject_org->guid;
            $relationship->subject_name = $subject_org->name; // duplicate data, but allows sorting members alphabetically
            
            try
            {
                $relationship->subject_email = EmailAddress::validate(trim(get_input('email')));        
            }
            catch (ValidationException $ex)
            {
                // ignore
            }            
        
            if ($org->guid == $relationship->subject_guid)
            {
                throw new RedirectException($relationship->msg('no_self'));
            }
        
            if (Relationship::query_for_user($org)
                ->where('type = ?', $relationship->type)
                ->where('subject_guid = ?', $relationship->subject_guid)
                ->exists())
            {
                return $this->duplicate_error($relationship);
            }            
        }
                
        $relationship->save();
        $relationship->post_feed_items();
        
        $this->save();
        
        $relationship->send_notifications(Relationship::Added);
        
        if ($relationship->invite_subject)
        {
            if ($relationship->send_invite_email())
            {                
                SessionMessages::add(strtr(__('network:invited'), array(
                    '{name}' => $relationship->get_subject_name()
                )));
            }
        }
        
        SessionMessages::add(strtr(__('network:added'), array(
            '{name}' => $relationship->get_subject_name(),
            '{type}' => $relationship->msg('header')
        )));
        $action->redirect($this->get_edit_url());    
    }
    
    private function clean_url($url)
    {
        if ($url)
        {
            $parsed = parse_url($url);
            $scheme = @$parsed['scheme'];
            if (!$scheme)
            {
                $url = "http://$url";
            }         
            else if ($scheme != 'http' && $scheme != 'https')
            {
                $url = '';
            }
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = preg_replace('/[\<\>\(\)\'\"]/', '', $url);
        }    
        return $url;
    }    
            
    private function delete_relationship($action)
    {
        $org = $this->get_container_entity();
        
        try
        {
            $relationship = $this->get_current_relationship($org);                        
        }
        catch (InvalidParameterException $ex)
        {
            // ignore
            $relationship = null;
        }
        
        if ($relationship)
        {
            $relationship->delete();
            
            SessionMessages::add(strtr(__('network:deleted'), array(
                '{name}' => $relationship->get_subject_name(),
                '{type}' => $relationship->msg('header')
            )));
        }       
        
        $action->redirect($this->get_edit_url());    
    }
    
    private function get_current_relationship($org)
    {
        $guid = (int)get_input('guid');
        $relationship = Relationship::query_for_user($org)->guid($guid)->get();
        if (!$relationship)
        {            
            throw new InvalidParameterException("invalid guid");
        }    
        return $relationship;
    }    
    
    private function save_relationship($action)
    {
        if (get_input('delete_relationship'))
        {
            return $this->delete_relationship($action);
        }
    
        $org = $this->get_container_entity();

        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            throw new NotFoundException();
        }                
        
        $relationship->set_content(get_input('content'));
        
        $subject_org = $relationship->get_subject_organization();
        
        if ($subject_org)
        {
            $relationship->subject_name = $subject_org->name;
        }
        else
        {
            $relationship->subject_name = get_input('name');
            $relationship->subject_email = EmailAddress::validate(get_input('email'));
            $relationship->subject_website = $this->clean_url(get_input('website'));            
            $relationship->subject_phone = get_input('phone_number');            
        }
        $relationship->set_self_approved();
        $relationship->save();
        
        if ($relationship->content)
        {
            $relationship->post_feed_items();
        }

        $relationship->send_notifications(Relationship::Added);        
        
        $this->save();
        
        SessionMessages::add(__('network:relationship_saved'));
        $action->redirect($this->get_edit_url());    
    }    
    
    private function edit_relationship_view()
    {
        $this->show_cancel_edit_button();
        
        $org = $this->get_container_entity();
        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            throw new NotFoundException();
        }
        
        return view('widgets/network_edit_relationship', array('widget' => $this, 'relationship' => $relationship));
    }
}