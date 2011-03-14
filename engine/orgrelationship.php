<?php

class OrgRelationship extends Entity
{
    const Partnership = 1;  // subject organization is partner of container_guid entity
    
    const Member = 2;       // container_guid entity represents a network, subject organization is a member
    const Membership = 3;   // subject organization is a member of container_guid network (inverse of OrgRelationship::Member)

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
}