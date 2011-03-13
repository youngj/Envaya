<?php

class NetworkMember extends Entity
{
    static $table_name = 'network_members';

    static $table_attributes = array(
        'org_guid' => null,        
        'name' => '',
        'email' => '',
        'website' => '',
        'logo_url' => '',
        'order' => 0
    );
        
    function get_member_organization()
    {
        return get_entity($this->org_guid);
    }
    
    function get_url()
    {
        $org = $this->get_member_organization();
        return $org ? $org->get_url() : $this->website;
    }
    
    function get_title()
    {
        $org = $this->get_member_organization();
        return $org ? $org->get_title() : ($this->name ?: $this->email ?: $this->website);
    }
}
