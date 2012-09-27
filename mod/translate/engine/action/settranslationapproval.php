<?php

class Action_SetTranslationApproval extends Action
{
    function before()
    {
        Permission_EditTranslation::require_for_entity($this->param('key'));
    }

    function process_input()
    {
        $key = $this->param('key');
        
        $approval = Input::get_int('approval');
        
        $translation = $this->param('translation');
        
        $translation->set_approved($approval > 0);        
        $translation->save();
        
        $key->update();

        SessionMessages::add(__('itrans:saved'));
        $this->redirect();
    }
}