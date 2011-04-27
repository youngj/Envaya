<?php

class Action_DeleteInterfaceTranslation extends Action
{
    function process_input()
    {    
        $this->require_login();
        
        $key = $this->param('key');
        
        $translation = $key->query_translations()->guid($this->param('translation_guid'))->get();
        if (!$translation)
        {
            return $this->not_found();
        }
        
        if (!$translation->can_edit())
        {
            throw new ValidationException(__('page:noaccess'));
        }
        
        $translation->disable();
        $translation->save();
        
        $key->update();
        $key->get_container_entity()->update();
        
        SessionMessages::add(__('itrans:deleted'));
        
        forward($key->get_url());
    }
}