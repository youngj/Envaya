<?php

class Widget_Contact extends Widget
{
    function render_view()
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

        try
        {
            validate_email_address($email);
        }
        catch (ValidationException $ex)
        {
            return redirect_back_error($ex->getMessage());
        }

        $org->email = $email;
        $this->public_email = sizeof(get_input_array('public_email')) ? 'yes' : 'no';

        $org->set_phone_number(get_input('phone_number'));
        $this->public_phone = sizeof(get_input_array('public_phone')) ? 'yes' : 'no';
        $org->contact_name = get_input('contact_name');
        $org->contact_title = get_input('contact_title');
        $org->street_address = get_input('street_address');
        $org->mailing_address = get_input('mailing_address');
        $org->save();
        $this->save();
    }
}
