<?php

class Action_SMSTemplate_Edit extends Action_ContactTemplate_Edit
{        
    function update_template($template)
    { 
        $content = Input::get_string('content');
        $template->set_content($content, true);
        $template->save_draft($content);
    }
}
