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
        
        'approval' => 0,
        
        'subject_notified' => 0,
        'invite_subject' => 0,
        
        // information about subject organization, if not envaya member
        'subject_name' => '',
        'subject_email' => '',
        'subject_website' => '',
        'subject_phone' => '',
        'subject_logo' => '',
                
        'order' => 0
    );    
    static $mixin_classes = array(
        'Mixin_Content'
    );    
           
       
    static $msg_ids = array(
        1 => array(
            'header' => 'network:partnerships',
            'no_self' => 'network:no_self_partnership',            
            'add_header' => "network:add_partnership",
            'add_confirm' => 'network:confirm_partner',
            'add_instructions' => 'network:add_partnership_instructions', 
            'add' => 'network:add_partnership',        
            'add_this_link' => 'network:add_partnership_link',
            'notify_added_subject' => 'network:notify_added_partnership_subject',
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
        return Organization::get_by_guid($this->subject_guid);
    }
    
    function get_url()
    {
        $org = $this->get_container_entity();
        $widget = $org->get_widget_by_class('Network');        
        return $widget->get_url() . "#r{$this->guid}";
    }
    
    function get_subject_url()
    {
        $org = $this->get_subject_organization();
        return $org && $org->is_approved() ? $org->get_url() : $this->subject_website;
    }
    
    function get_subject_email()
    {
        $org = $this->get_subject_organization();
        return $org ? $org->email : $this->subject_email;
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
    
    function make_reverse_relationship()
    {
        $reverse = new OrgRelationship();
        $reverse->type = OrgRelationship::get_reverse_type($this->type);
        $reverse->container_guid = $this->subject_guid;
        $reverse->subject_guid = $this->container_guid;
        $reverse->subject_name = $this->get_container_entity()->name;
        return $reverse;
    }
    
    public function get_feed_names()
    {
        $subject = $this->get_subject_organization();
        return $subject ? $subject->get_feed_names() : array();
    }
    
    function post_feed_items()
    {
        if (FeedItem::query()
            ->where('subject_guid = ?', $this->guid)
            ->where('time_posted > ?', time() - 60 * 60 * 24)
            ->is_empty())
        {
            $org = $this->get_container_entity();
            FeedItem_Relationship::post($org, $this);
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
            $this->approval |= static::SelfApproved;
        }
        else
        {
            $this->approval &= ~static::SelfApproved;
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
            $this->approval |= static::SubjectApproved;
        }
        else
        {
            $this->approval &= ~static::SubjectApproved;
        }
    }    
    
    function msg($msg_type, $lang = null)
    {
        return static::msg_for_type($this->type, $msg_type, $lang);
    }
        
    static function msg_for_type($type, $msg_type, $lang = null)
    {       
        return __(@static::$msg_ids[$type][$msg_type] 
            ?: @static::$msg_ids['default'][$msg_type]
            ?: "network:$msg_type", $lang);
    }
        
    function send_notification_email()
    {
        if ($this->subject_notified)
        {
            return false;
        }
    
        $org = $this->get_container_entity();

        $subject_org = $this->get_subject_organization();        
        $widget = $org->get_widget_by_class('Network');
        $reverse = $this->get_reverse_relationship();
        
        if ($subject_org && $widget && $reverse && $subject_org->email && $subject_org->is_notification_enabled(Notification::Network))
        {    
            OutgoingMail::create(
                strtr($this->msg('notify_added_subject', $subject_org->language), array(
                    '{name}' => $org->name, '{subject}' => $subject_org->name
                )), 
                view('emails/network_relationship_added', array(
                    'relationship' => $this,
                    'reverse' => $reverse,
                    'widget' => $widget
                ))
            )->send_to_user($subject_org);   
            $this->subject_notified = true;
            $this->save();
            return true;
        }
        return false;
    }
    
    function send_invite_email()
    {
        $org = $this->get_container_entity();
        $widget = $org->get_widget_by_class('Network');
        $email = $this->subject_email;
        
        if (!$email || $this->subject_guid || !$widget)
        {
            return false;
        }
        
        $invitedEmail = InvitedEmail::get_by_email($email);

        if (!$invitedEmail->can_send_invite())
        {
            return false;
        }

        $mail = OutgoingMail::create(
            strtr(__('network:notify_invited_subject', $org->language), array('{name}' => $org->name)),
            view('emails/network_relationship_invite', array(
                'relationship' => $this,
                'invited_email' => $invitedEmail,
                'widget' => $widget
            ))
        );
        $mail->addTo($email, $this->subject_name);        
        $mail->send();
        
        $invitedEmail->mark_invite_sent();
        
        $this->subject_notified = true;
        $this->save();
            
        return true;        
    }    
}