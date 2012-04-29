<?php

/*
 * Represents an account for an individual user (not an organization) on Envaya.
 */
class Person extends User
{
    // event names
    const Registered = 'person_registered';

    function init_default_widgets()
    {
        Widget_PersonProfile::get_or_init_for_entity($this);
    }
    
    function set_defaults()
    {
        $this->set_design_setting('theme_id', Theme_PersonProfile::get_subtype_id());
    }
    
    function get_default_icon_props($size = '')
    {
        return array(
            'url' => "/_media/images/personmedium.gif",
            'width' => 100,
            'height' => 100
        );
    }    
}
