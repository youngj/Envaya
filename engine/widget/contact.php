<?php

/* 
 * A structured widget that displays an organization's contact information,
 * (phone number, address, email, etc.). It stores values on the organization 
 * itself so that the contact information can be easily queried throughout the system.
 */
class Widget_Contact extends Widget
{
    static $default_menu_order = 90;
    static $default_widget_name = 'contact';
    
    function get_default_title()
    {
        return __("widget:contact");
    }

    function render_view($args = null)
    {
        return view("widgets/contact_view", array('widget' => $this));
    }
    
    function render_edit()
    {
        return view("widgets/contact_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $org = $this->get_container_entity();
        
        Permission_EditUserSettings::require_for_entity($org);

        $email = trim(Input::get_string('email'));

        EmailAddress::validate($email);

        $org->set_email($email);
        $org->set_metadata('public_email', sizeof(Input::get_array('public_email')) ? 'yes' : 'no');

        $org->set_phone_number(Input::get_string('phone_number'));
        $org->set_metadata('public_phone', sizeof(Input::get_array('public_phone')) ? 'yes' : 'no');
        $org->set_metadata('contact_name', Input::get_string('contact_name'));
        $org->set_metadata('contact_title', Input::get_string('contact_title'));
        $org->set_metadata('street_address', Input::get_string('street_address'));
        $org->set_metadata('mailing_address', Input::get_string('mailing_address'));
        $org->save();
        $this->save();
    }
}
