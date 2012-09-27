<?php

class Action_SMSTemplate_Add extends Action_ContactTemplate_Add
{
    function init_template($template)
    {
        $template->set_content(Input::get_string('content'));
    }
}