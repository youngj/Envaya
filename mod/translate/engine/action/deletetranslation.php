<?php

class Action_DeleteTranslation extends Action
{
    function before()
    {
        Permission_EditTranslation::require_for_entity($this->param('translation'));        
    }

    function process_input()
    {            
        $key = $this->param('key');
        $translation = $this->param('translation');
        
        $translation->disable();
        $translation->save();
        
        $key->update(true);
        
        $user = $translation->get_owner_entity();
        if ($user)
        {
            $language = $key->get_language();
            $language->get_stats_for_user($user)->update();        
        }
        
        SessionMessages::add(__('itrans:deleted'));        
        $this->redirect();
    }
}