<?php

class Action_EmailTemplate_Add extends Action_ContactTemplate_Add
{
    function init_template($template)
    {
        $template->from = get_input('from');
        $template->subject = get_input('subject');        
        $template->set_content(get_input('content'));
    }
}