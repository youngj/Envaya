<?php

/* 
 * A widget that shows an organization's partner organizations (OrgRelationships)
 * and allows the organization to add/remove partner organizations.
 */
class Widget_Network extends Widget
{
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
            case 'approve':                 return $this->approve_relationship_view();
            
            default:                 
                return view("widgets/network_edit", array('widget' => $this));
        }    
    }

    function process_input($action)
    {        
        switch (get_input('action'))
        {
            case 'delete_relationship':  return $this->delete_relationship();
            case 'save_relationship':    return $this->save_relationship();
            case 'add_relationship':     return $this->add_relationship();
            
            case 'approve':              return $this->approve_relationship();
            
            default: 
                $this->save();
                return;
        }
    }
    
    private function show_cancel_edit_button()
    {
        $submenu = PageContext::get_submenu('edit');
        $submenu->clear();
        $submenu->add_item(__("cancel"), $this->get_edit_url());
    }
    
    private function add_relationship_view()
    {
        $this->show_cancel_edit_button();
                        
        $org_guid = (int)get_input('org_guid');
        $org = Organization::get_by_guid($org_guid);
        
        $type = (int)get_input('type');
        if (!OrgRelationship::is_valid_type($type))
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
        return redirect_back_error(strtr(__('network:duplicate'), array(
                '{name}' => $relationship->get_subject_name(),
                '{type}' => $relationship->msg('header')
        )));
    }
    
    private function add_relationship()
    {
        $org = $this->get_container_entity();
        
        $reverse = null;
    
        $relationship = new OrgRelationship();
        $relationship->type = (int)get_input('type');
        $relationship->container_guid = $org->guid;
        $relationship->set_self_approved();
        
        $relationship->set_content(get_input('content'));

        if (!OrgRelationship::is_valid_type($relationship->type))
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
            $relationship->subject_email = validate_email_address(trim(get_input('email')));
            
            $matchingRelationships = $org->query_relationships()
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
                $relationship->subject_email = validate_email_address(trim(get_input('email')));        
            }
            catch (ValidationException $ex)
            {
                // ignore
            }            
        
            if ($org->guid == $relationship->subject_guid)
            {
                return redirect_back_error($relationship->msg('no_self'));
            }
        
            if ($org->query_relationships()
                ->where('type = ?', $relationship->type)
                ->where('subject_guid = ?', $relationship->subject_guid)
                ->exists())
            {
                return $this->duplicate_error($relationship);
            }
            
            if ($org->is_approved() && $subject_org->query_relationships()
                ->where('type = ?', $relationship->type)
                ->where('subject_guid = ?', $org->guid)
                ->is_empty())
            {
                $reverse = $relationship->make_reverse_relationship();
                $reverse->set_subject_approved();
                $reverse->save();                                                                
            }
        }
                
        $relationship->save();
        $relationship->post_feed_items();
        
        $this->save();

        if ($org->is_approved())
        {
            if ($reverse)
            {                    
                $relationship->send_notification_email();
            }
            
            if ($relationship->invite_subject)
            {
                if ($relationship->send_invite_email())
                {                
                    SessionMessages::add(strtr(__('network:invited'), array(
                        '{name}' => $relationship->get_subject_name()
                    )));
                }
            }
        }
        
        SessionMessages::add(strtr(__('network:added'), array(
            '{name}' => $relationship->get_subject_name(),
            '{type}' => $relationship->msg('header')
        )));
        forward($this->get_edit_url());    
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
            
    private function delete_relationship()
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
            $reverse = $relationship->get_reverse_relationship();
            if ($reverse && !$reverse->is_self_approved())
            {
                $reverse->delete();
            }            
            $relationship->delete();
            
            SessionMessages::add(strtr(__('network:deleted'), array(
                '{name}' => $relationship->get_subject_name(),
                '{type}' => $relationship->msg('header')
            )));
        }       
        
        return forward($this->get_edit_url());        
    }
    
    private function get_current_relationship($org)
    {
        $guid = (int)get_input('guid');
        $relationship = $org->query_relationships()->guid($guid)->get();
        if (!$relationship)
        {            
            throw new InvalidParameterException("invalid guid");
        }    
        return $relationship;
    }    
    
    private function approve_relationship_view()
    {
        $org = $this->get_container_entity();
        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            throw new NotFoundException();
        }        
        
        $this->save();
        
        return view('widgets/network_approve_relationship', array('widget' => $this, 'relationship' => $relationship));
    }    
    
    private function approve_relationship()
    {    
        $org = $this->get_container_entity();

        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            throw new NotFoundException();
        }                
        
        $relationship->set_self_approved();
        $relationship->save();

        $this->save();

        SessionMessages::add(__('network:relationship_saved'));
        return forward($this->get_edit_url());
    }    
    
    
    private function save_relationship()
    {
        if (get_input('delete_relationship'))
        {
            return $this->delete_relationship();
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
            $relationship->subject_email = validate_email_address(get_input('email'));
            $relationship->subject_website = $this->clean_url(get_input('website'));            
            $relationship->subject_phone = get_input('phone_number');            
        }
        $relationship->set_self_approved();
        $relationship->save();
        
        if ($relationship->content)
        {
            $relationship->post_feed_items();
        }

        if ($org->is_approved())
        {
            $relationship->send_notification_email();
        }
        
        $this->save();
        
        SessionMessages::add(__('network:relationship_saved'));
        return forward($this->get_edit_url());
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