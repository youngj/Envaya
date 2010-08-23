<?php

class WidgetHandler_Contact extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/contact_view", array('widget' => $widget));
    }
    
    function edit($widget)
    {
        return view("widgets/contact_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        $org = $widget->get_container_entity();

        $email = trim(get_input('email'));

        validate_email_address($email);

        $org->email = $email;
        $widget->public_email = sizeof(get_input_array('public_email')) ? 'yes' : 'no';

        $org->phone_number = get_input('phone_number');
        $widget->public_phone = sizeof(get_input_array('public_phone')) ? 'yes' : 'no';
        $org->contact_name = get_input('contact_name');
        $org->contact_title = get_input('contact_title');
        $org->street_address = get_input('street_address');
        $org->mailing_address = get_input('mailing_address');
        $org->save();
        $widget->save();
    }
}
