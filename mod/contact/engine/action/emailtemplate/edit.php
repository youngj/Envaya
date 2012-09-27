<?php

class Action_EmailTemplate_Edit extends Action_ContactTemplate_Edit
{        
    function update_template($template)
    { 
        $template->subject = Input::get_string('subject');                        
        $content = Input::get_string('content');
        $template->set_content($content);
        $template->save_draft($content);
        $template->from = Input::get_string('from');
    }
}
