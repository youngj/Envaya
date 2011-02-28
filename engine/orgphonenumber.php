<?php

/*
 * A phone number that an organization has registered for the SMS interface.
 * (Different from phone number(s) listed on the contact page)
 */
class OrgPhoneNumber extends Model
{
    static $table_name = 'org_phone_numbers';
    static $table_attributes = array(
        'phone_number' => '',
        'org_guid' => 0,
        'confirmed' => 0,
    );
    
    function get_org()
    {
        return get_entity($this->org_guid);
    }
}
