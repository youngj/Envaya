<?php

class Action_SetTranslationApproval extends Action
{
    function process_input()
    {
        $this->require_admin();
        
        $approval = (int)get_input('approval');
        
        $translation = $this->param('translation');
        
        $translation->set_approved($approval > 0);        
        $translation->save();

        SessionMessages::add(__('itrans:saved'));
        $this->redirect();
    }
}