<?php

class Action_EmailTemplate_Add extends Action_ContactTemplate_Add
{
    function init_template($template)
    {
        $template->from = Input::get_string('from');
        $template->subject = Input::get_string('subject');        
        $template->set_content(Input::get_string('content'));
    }
}