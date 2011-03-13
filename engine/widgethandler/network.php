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
            case 'add_member': 
                PageContext::add_submenu_item(__("cancel"), $widget->get_edit_url(), 'edit', true);
                
                return view("widgets/network_add_member", array('widget' => $widget));
            default:                 
                return view("widgets/network_edit", array('widget' => $widget));
        }    
    }

    function save($widget)
    {        
        switch (get_input('action'))
        {
            case 'delete_member':  return $this->delete_member($widget);
            case 'add_member':  return $this->add_member($widget);
            default: 
                $widget->save();
                return;
        }
    }
    
    private function add_member($widget)
    {
        $org = $widget->get_container_entity();
    
        $member = new NetworkMember();
        $member->container_guid = $org->guid;

        $member_org = Organization::query()->where('e.guid = ?', (int)get_input('org_guid'))->get();        
        if (!$member_org)
        {
            $member->name = get_input('name');
            $member->email = get_input('email');
            $member->website = get_input('website');            
            $member->org_guid = 0;
            
            $matchingMembers = $org->query_network_members()
                ->where('name = ?', $member->name)
                ->where('email = ?', $member->email)
                ->where('website = ?', $member->website)
                ->count();

            if ($matchingMembers > 0)
            {
                return action_error(sprintf(__('network:already_member'), $member->get_title()));
            }            
            
            try
            {
                validate_email_address($member->email);
            }
            catch (Exception $ex)
            {
                return action_error($ex->getMessage());
            }
            
            if (get_input('invite'))
            {
                $member->invite_code = substr(generate_random_cleartext_password(), 0, 20);
                
                // todo: actually send invite email
                
                system_message(sprintf(__('network:member_invited'), $member->get_title()));
            }
        }
        else
        {            
            $member->org_guid = $member_org->guid;
            $member->name = $member_org->name; // duplicate data, but allows sorting members alphabetically
        
            if ($org->guid == $member->org_guid)
            {
                return action_error(__('network:no_self_member'));
            }
        
            if ($org->query_network_members()->where('org_guid = ?', $member->org_guid)->count() > 0)
            {
                return action_error(sprintf(__('network:already_member'), $member->get_title()));
            }
        }
                
        $member->save();
        $widget->save();

        system_message(sprintf(__('network:member_added'), $member->get_title()));
        forward($widget->get_edit_url());
    }
    
    private function delete_member($widget)
    {
        $member_guid = (int)get_input('member_guid');
        if ($member_guid)
        {        
            $org = $widget->get_container_entity();
            
            $member = $org->query_network_members()->where('e.guid = ?', $member_guid)->get();
            if ($member)
            {
                $member->delete();            
                system_message(sprintf(__('network:member_deleted'), $member->get_title()));
            }           
            $widget->save();
            return forward($widget->get_edit_url());        
        }    
    }   
}

