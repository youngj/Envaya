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
                $this->set_submenu($widget);
                return view("widgets/network_add_member", array('widget' => $widget));
                
            case 'add_membership':
                $this->set_submenu($widget);
                return view("widgets/network_add_membership", array('widget' => $widget));

            case 'add_partnership':
                $this->set_submenu($widget);
                return view("widgets/network_add_partnership", array('widget' => $widget));                
                
            default:                 
                return view("widgets/network_edit", array('widget' => $widget));
        }    
    }

    function save($widget)
    {        
        switch (get_input('action'))
        {
            case 'delete_member':      return $this->delete_member($widget);
            case 'delete_membership':  return $this->delete_membership($widget);
            case 'delete_partnership': return $this->delete_partnership($widget);
            
            case 'add_member':       return $this->add_member($widget);
            case 'add_membership':   return $this->add_membership($widget);
            case 'add_partnership':  return $this->add_partnership($widget);
            
            default: 
                $widget->save();
                return;
        }
    }
    
    private function set_submenu($widget)
    {
        PageContext::add_submenu_item(__("cancel"), $widget->get_edit_url(), 'edit', true);
    }    
    
    private function add_relationship($widget, $relationshipType, $messages)
    {
        $org = $widget->get_container_entity();
    
        $relationship = new OrgRelationship();
        $relationship->type = $relationshipType;
        $relationship->container_guid = $org->guid;

        $subject_org = Organization::query()->where('e.guid = ?', (int)get_input('org_guid'))->get();        
        if (!$subject_org) // subject_org not an envaya member
        {
            $relationship->subject_name = get_input('name');
            $relationship->subject_email = get_input('email');
            $relationship->subject_website = get_input('website');            
            $relationship->subject_guid = 0;
            
            $matchingRelationships = $org->query_relationships()
                ->where('`type` = ?', $relationship->type)
                ->where('subject_name = ?', $relationship->subject_name)
                ->where('subject_email = ?', $relationship->subject_email)
                ->where('subject_website = ?', $relationship->subject_website)
                ->count();

            if ($matchingRelationships > 0)
            {
                return action_error(sprintf($messages['duplicate'], $relationship->get_subject_name()));
            }            
            
            try
            {
                validate_email_address($relationship->subject_email);
            }
            catch (Exception $ex)
            {
                return action_error($ex->getMessage());
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
                return action_error($messages['no_self']);
            }
        
            if ($org->query_relationships()
                ->where('type = ?', $relationship->type)
                ->where('subject_guid = ?', $relationship->subject_guid)
                ->count() > 0)
            {
                return action_error(sprintf($messages['duplicate'], $relationship->get_subject_name()));
            }
            
            $reverse = new OrgRelationship();
            $reverse->type = OrgRelationship::get_reverse_type($relationship->type);
            $reverse->container_guid = $relationship->subject_guid;
            $reverse->subject_guid = $relationship->container_guid;
            $reverse->subject_name = $org->name;
            $reverse->save();
        }
                
        $relationship->save();
        $relationship->post_feed_items();
        
        $widget->save();

        system_message(sprintf($messages['added'], $relationship->get_subject_name()));
        forward($widget->get_edit_url());    
    }
    
    private function add_member($widget)
    {
        $this->add_relationship($widget, OrgRelationship::Member, array(
            'duplicate' => __('network:already_member'),
            'added' => __('network:member_added'),
            'no_self' => __('network:no_self_member'),
        ));
    }
    
    private function add_membership($widget)
    {
        $this->add_relationship($widget, OrgRelationship::Membership, array(
            'duplicate' => __('network:already_membership'),
            'added' => __('network:membership_added'),
            'no_self' => __('network:no_self_member'),
        ));
    }
    
    private function add_partnership($widget)
    {
        $this->add_relationship($widget, OrgRelationship::Partnership, array(
            'duplicate' => __('network:already_partnership'),
            'added' => __('network:partnership_added'),
            'no_self' => __('network:no_self_partnership'),
        ));
    }    
            
    private function delete_relationship($widget, $messages)
    {
        $relationship_guid = (int)get_input('guid');
        if ($relationship_guid)
        {        
            $org = $widget->get_container_entity();
            
            $relationship = $org->query_relationships()->where('e.guid = ?', $relationship_guid)->get();
            if ($relationship)
            {
                $reverse = $relationship->get_reverse_relationship();
                if ($reverse)
                {
                    $reverse->delete();
                }            
                $relationship->delete();
                system_message(sprintf($messages['deleted'], $relationship->get_subject_name()));
            }           
            return forward($widget->get_edit_url());        
        }    
    }
    
    private function delete_partnership($widget)
    {
        $this->delete_relationship($widget, array(
            'deleted' => __('network:partnership_deleted'),
        ));
    }
    
    private function delete_membership($widget)
    {
        $this->delete_relationship($widget, array(
            'deleted' => __('network:membership_deleted'),
        ));
    }
    
    private function delete_member($widget)
    {
        $this->delete_relationship($widget, array(
            'deleted' => __('network:member_deleted'),
        ));
    }
}