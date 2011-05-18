<?php

class Action_DeleteInterfaceTranslation extends Action
{
    function process_input()
    {    
        $this->require_login();
        
        $key = $this->param('key');
        $translation = $this->param('translation');
        
        if (!$translation->can_edit())
        {
            throw new ValidationException(__('page:noaccess'));
        }
        
        $translation->disable();
        $translation->save();
        
        $key->update();
        $key->get_container_entity()->update();
        
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