<?php

class Widget_PersonProfile extends Widget
{
    function render_view($args = null)
    {
        return view('widgets/personprofile_view', array('widget' => $this));
    }
    
    function get_title()
    {
        return __('user:profile_title');
    }
}
