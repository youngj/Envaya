<?php

/*
 * Represents an account for an individual user (not an organization) on Envaya.
 */
class Person extends User
{
    function init_default_widgets()
    {
        $this->get_widget_by_class('PersonProfile')->save();
    }
    
    function set_defaults()
    {
        $this->set_design_setting('theme_name', "personprofile");
    }
    
    function get_default_icon_props($size = '')
    {
        return array(
            'url' => abs_url("/_media/images/personmedium.gif"),
            'width' => 100,
            'height' => 100
        );
    }    
}
