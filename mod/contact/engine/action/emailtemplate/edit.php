<?php

class Action_EmailTemplate_Edit extends Action_ContactTemplate_Edit
{        
    function update_template($template)
    { 
        $template->subject = get_input('subject');                        
        $content = get_input('content');
        $template->set_content($content);
        $template->save_draft($content);
        $template->from = get_input('from');
    }
}
