<?php

class WidgetHandler_Network extends WidgetHandler
{
    function get_default_title($widget)
    {
        return __('network:title');
    }

    function view($widget)
    {
        return view("widgets/network_view", array('widget' => $widget));
    }   

    function edit($widget)
    {                
        switch (get_input('action'))       
        {
            case 'add_relationship':        return $this->add_relationship_view($widget);                               
            case 'approve_relationship':    return $this->approve_relationship_view($widget);                
            case 'edit_relationship':       return $this->edit_relationship_view($widget);                                
            
            default:                 
                return view("widgets/network_edit", array('widget' => $widget));
        }    
    }

    function save($widget)
    {        
        switch (get_input('action'))
        {
            case 'delete_relationship':  return $this->delete_relationship($widget);
            case 'save_relationship':    return $this->save_relationship($widget);
            case 'approve_relationship': return $this->approve_relationship($widget);                        
            case 'add_relationship':     return $this->add_relationship($widget);
            
            default: 
                $widget->save();
                return;
        }
    }
    
    private function show_cancel_edit_button($widget)
    {
        PageContext::add_submenu_item(__("cancel"), $widget->get_edit_url(), 'edit', true);                           
    }
    
    private function add_relationship_view($widget)
    {
        $this->show_cancel_edit_button($widget);
                        
        $org_guid = (int)get_input('org_guid');
        $org = $org_guid ? Organization::query()->where('e.guid = ?', $org_guid)->get() : null;
        
        $type = (int)get_input('type');
        if (!OrgRelationship::is_valid_type($type))
        {
            return not_found();
        }
                
        return view('widgets/network_add_relationship', array(
            'widget' => $widget, 
            'org' => $org,
            'type' => $type
        ));            
    }    
    
    private function add_relationship($widget)
    {
        $org = $widget->get_container_entity();
    
        $relationship = new OrgRelationship();
        $relationship->type = (int)get_input('type');
        $relationship->container_guid = $org->guid;
        $relationship->set_self_approved();

        if (!OrgRelationship::is_valid_type($relationship->type))
        {
            return not_found();
        }
        
        $subject_org = Organization::query()->where('e.guid = ?', (int)get_input('org_guid'))->get();        
        if (!$subject_org) // subject_org not an envaya member
        {
            $relationship->subject_name = get_input('name');
            $relationship->subject_email = $this->validate_email(get_input('email'));
            $relationship->subject_website = $this->clean_url(get_input('website'));
            $relationship->subject_guid = 0;
            
            $matchingRelationships = $org->query_relationships()
                ->where('`type` = ?', $relationship->type)
                ->where('subject_name = ?', $relationship->subject_name)
                ->where('subject_email = ?', $relationship->subject_email)
                ->where('subject_website = ?', $relationship->subject_website)
                ->count();

            if ($matchingRelationships > 0)
            {
                return action_error(sprintf($relationship->__('duplicate'), $relationship->get_subject_name()));
            }

            if (get_input('invite'))
            {
                $relationship->invite_code = substr(generate_random_cleartext_password(), 0, 20);
                
                // todo: actually send invite email                
                system_message(sprintf(__('network:invited'), $relationship->get_subject_name()));
            }
        }
        else // subject_org already an envaya member
        {            
            $relationship->subject_guid = $subject_org->guid;
            $relationship->subject_name = $member_org->name; // duplicate data, but allows sorting members alphabetically
        
            if ($org->guid == $relationship->subject_guid)
            {
                return action_error($relationship->__('no_self'));
            }
        
            if ($org->query_relationships()
                ->where('type = ?', $relationship->type)
                ->where('subject_guid = ?', $relationship->subject_guid)
                ->count() > 0)
            {
                return action_error(sprintf($relationship->__('duplicate'), $relationship->get_subject_name()));
            }
            
            if ($subject_org->query_relationships()
                ->where('type = ?', $relationship->type)
                ->where('subject_guid = ?', $org->guid)
                ->count() == 0)
            {            
                $reverse = new OrgRelationship();
                $reverse->type = OrgRelationship::get_reverse_type($relationship->type);
                $reverse->container_guid = $relationship->subject_guid;
                $reverse->subject_guid = $org->guid;
                $reverse->subject_name = $org->name;
                $reverse->set_subject_approved();
                $reverse->save();
            }
        }
                
        $relationship->save();
        $relationship->post_feed_items();
        
        $widget->save();

        system_message(sprintf($relationship->__('added'), $relationship->get_subject_name()));
        forward($widget->get_edit_url());    
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
            else if ($scheme != 'http' || $scheme != 'https')
            {
                $url = '';
            }
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = preg_replace('/[\<\>\(\)\'\"]/', '', $url);
        }    
        return $url;
    }    
            
    private function delete_relationship($widget)
    {
        $org = $widget->get_container_entity();
        
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
            
            system_message(sprintf($relationship->__('deleted'), $relationship->get_subject_name()));
        }       
        
        return forward($widget->get_edit_url());        
    }
    
    private function get_current_relationship($org)
    {
        $guid = (int)get_input('guid');
        $relationship = $org->query_relationships()->where('e.guid = ?', $guid)->get();
        if (!$relationship)
        {            
            throw new InvalidParameterException("invalid guid");
        }    
        return $relationship;
    }    
    
    private function approve_relationship_view($widget)
    {
        $org = $widget->get_container_entity();
        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            return not_found();
        }        
        
        return view('widgets/network_approve_relationship', array('widget' => $widget, 'relationship' => $relationship));
    }
    
    private function approve_relationship($widget)
    {
        $org = $widget->get_container_entity();
        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            return not_found();
        }        
        
        $relationship->set_self_approved();
        $relationship->save();
        
        system_message(__('network:relationship_approved'));
        return forward($widget->get_edit_url());        
    }
    
    private function save_relationship($widget)
    {
        $org = $widget->get_container_entity();

        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            return not_found();
        }                
        
        $relationship->set_content(get_input('content'), true);
        
        $subject_org = $relationship->get_subject_organization();
        
        if ($subject_org)
        {
            $relationship->subject_name = $subject_org->name;
        }
        else
        {
            $relationship->subject_name = get_input('name');
            $relationship->subject_email = $this->validate_email(get_input('email'));
            $relationship->subject_website = $this->clean_url(get_input('website'));            
        }
        $relationship->set_self_approved();
        $relationship->save();
        
        system_message(__('network:relationship_saved'));
        return forward($widget->get_edit_url());
    }    
    
    private function validate_email($email)
    {
        try
        {
            validate_email_address($email);
            return $email;
        }
        catch (Exception $ex)
        {
            return action_error($ex->getMessage());
        }                
    }
    
    private function edit_relationship_view($widget)
    {
        $this->show_cancel_edit_button($widget);
        
        $org = $widget->get_container_entity();
        try
        {
            $relationship = $this->get_current_relationship($org);
        }
        catch (InvalidParameterException $ex)
        {
            return not_found();
        }
        
        return view('widgets/network_edit_relationship', array('widget' => $widget, 'relationship' => $relationship));
    }
}