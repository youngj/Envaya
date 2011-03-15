<?php

class OrgRelationship extends Entity
{
    const Partnership = 1;  // subject organization is partner of container_guid entity
    
    const Member = 2;       // container_guid entity represents a network, subject organization is a member
    const Membership = 3;   // subject organization is a member of container_guid network (inverse of OrgRelationship::Member)

    const SelfApproved = 1;
    const SubjectApproved = 2;
    
    static $table_name = 'org_relationships';

    static $table_attributes = array(
        'type' => 0,            // RelationshipType between container_guid entity and subject 
        'subject_guid' => null, // guid of subject entity to which container_guid entity is related
        
        'content' => '',
        'language' => '',
        'approval' => 0,
        
        // information about subject organization, if not envaya member
        'subject_name' => '',
        'subject_email' => '',
        'subject_website' => '',
        'subject_logo' => '',
        
        'invite_code' => '', 
        
        'order' => 0
    );    
                                  
    static $msg_ids = array(
        2 => array(
            'header' => 'network:members',
            'approve_header' => 'network:approve_member',
            'deleted' => 'network:member_deleted',
            'duplicate' => 'network:already_member',
            'added' => 'network:member_added',
            'no_self' => 'network:no_self_member',            
            'add_header' => 'network:add_member',
            'can_add_unregistered' => 'network:can_add_unregistered_member',
            'add_confirm' => 'network:confirm_member',
            'add_instructions' => 'network:add_member_instructions',            
            'edit_header' => 'network:edit_members',
            'empty' => "network:no_members",
            'add' => 'network:add_member',
            'add_another' => 'network:add_another_member',        
            'confirm_delete' => 'network:confirm_delete_member',
        ),
        3 => array(
            'header' => 'network:memberships',
            'approve_header' => 'network:approve_membership',
            'deleted' => 'network:membership_deleted',
            'duplicate' => 'network:already_membership',
            'added' => 'network:membership_added',
            'no_self' => 'network:no_self_member',            
            'add_header' => 'network:add_membership',
            'add_confirm' => 'network:confirm_network',
            'not_shown' => 'network:network_not_shown',
            'can_add_unregistered' => 'network:can_add_unregistered_network',
            'add_instructions' => 'network:add_membership_instructions',  
            'edit_header' => 'network:edit_memberships',         
            'empty' => 'network:no_memberships',
            'add' => 'network:add_membership',        
            'add_another' => 'network:add_another_membership',        
            'confirm_delete' => 'network:confirm_delete_membership',            
            'add_link' => 'network:add_membership_link',
        ),
        1 => array(
            'header' => 'network:partnerships',
            'approve_header' => 'network:approve_partnership',
            'deleted' => 'network:partnership_deleted',
            'duplicate' => 'network:already_partnership',
            'added' => 'network:partnership_added',
            'no_self' => 'network:no_self_partnership',            
            'add_header' => "network:add_partnership",
            'can_add_unregistered' => 'network:can_add_unregistered_partner',
            'add_confirm' => 'network:confirm_partner',
            'add_instructions' => 'network:add_partnership_instructions', 
            'edit_header' => 'network:edit_partnerships', 
            'empty' => 'network:no_partnerships',
            'add' => 'network:add_partnership',        
            'add_another' => 'network:add_another_partnership',        
            'confirm_delete' => 'network:confirm_delete_partnership',
            'add_link' => 'network:add_partnership_link',
        ),
        'default' => array(
            'not_shown' => 'network:org_not_shown',
        )
    );    
    
    static function is_valid_type($type)
    {
        return isset(static::$msg_ids[$type]);
    }
    
    static function get_reverse_type($type)
    {
        switch ($type)
        {
            case static::Partnership:   return static::Partnership;
            case static::Member:        return static::Membership;
            case static::Membership:    return static::Member;
            default: throw new Exception("Invalid relationship type: {$type}");
        }
    }    
                       
    function get_subject_organization()
    {
        return get_entity($this->subject_guid);
    }
    
    function get_url()
    {
        $org = $this->get_container_entity();
        $widget = $org->get_widget_by_class('WidgetHandler_Network');        
        return $widget->get_url() . "#r{$this->guid}";
    }
    
    function get_subject_url()
    {
        $org = $this->get_subject_organization();
        return $org ? $org->get_url() : $this->subject_website;
    }
    
    function get_subject_name()
    {
        $org = $this->get_subject_organization();
        return $org ? $org->get_title() : ($this->subject_name ?: $this->subject_email ?: $this->subject_website);
    }
    
    function get_reverse_relationship()
    {
        $org = $this->get_subject_organization();
        if ($org)
        {
            return $org->query_relationships()
                ->where('`type` = ?', static::get_reverse_type($this->type)) 
                ->where('subject_guid = ?', $this->container_guid)->get();
        }
        return null;
    }
    
    public function get_feed_names()
    {
        $subject = $this->get_subject_organization();
        return $subject ? $subject->get_feed_names() : array();
    }
    
    function post_feed_items()
    {
        if (FeedItem::query()->where('subject_guid = ?', $this->guid)->count() == 0)
        {
            $org = $this->get_container_entity();
            post_feed_items($org, 'relationship', $this);
        }
    }        
    
    function get_feed_heading_format()
    {
        switch ($this->type)
        {
            case static::Partnership:   return __('network:feed_partnership');
            case static::Member:        return __('network:feed_member');
            case static::Membership:    return __('network:feed_membership');
            default: throw new Exception("Invalid relationship type: {$this->type}");
        }    
    }
    
    function is_self_approved()
    {
        return ($this->approval & static::SelfApproved) != 0;
    }

    function set_self_approved($approved = true)
    {
        if ($approved)
        {
            $this->approval = $this->approval | static::SelfApproved;
        }
        else
        {
            $this->approval = $this->approval & ~static::SelfApproved;
        }
    }

    function is_subject_approved()
    {
        return ($this->approval & static::SubjectApproved) != 0;
    }

    function set_subject_approved($approved = true)
    {
        if ($approved)
        {
            $this->approval = $this->approval | static::SubjectApproved;
        }
        else
        {
            $this->approval = $this->approval & ~static::SubjectApproved;
        }
    }    
    
    function __($msg_type, $lang = null)
    {
        return static::msg($this->type, $msg_type, $lang);
    }
        
    static function msg($type, $msg_type, $lang = null)
    {       
        return __(@static::$msg_ids[$type][$msg_type] ?: static::$msg_ids['default'][$msg_type], $lang);
    }
}