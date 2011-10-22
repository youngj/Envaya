<?php

/* 
 * A structured widget that displays an organization's contact information,
 * (phone number, address, email, etc.). It stores values on the organization 
 * itself so that the contact information can be easily queried throughout the system.
 */
class Widget_Contact extends Widget
{
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

        $email = trim(get_input('email'));

        EmailAddress::validate($email);

        $org->set_email($email);
        $org->set_metadata('public_email', sizeof(get_input_array('public_email')) ? 'yes' : 'no');

        $org->set_phone_number(get_input('phone_number'));
        $org->set_metadata('public_phone', sizeof(get_input_array('public_phone')) ? 'yes' : 'no');
        $org->set_metadata('contact_name', get_input('contact_name'));
        $org->set_metadata('contact_title', get_input('contact_title'));
        $org->set_metadata('street_address', get_input('street_address'));
        $org->set_metadata('mailing_address', get_input('mailing_address'));
        $org->save();
        $this->save();
    }
}
