<?php

class Action_SaveTranslationDraft extends Action
{
    function process_input()
    {
        $this->set_content_type('text/javascript');
        
        $this->require_login();
        
        $value = get_input('content');
        $key = $this->param('key');
        
        $user = Session::get_loggedin_user();
        
        $translation = $key->get_draft_translation_for_user($user);            
        if (!$translation)
        {
            $translation = $key->new_translation();
            $translation->source = Translation::Human;
            $translation->owner_guid = $user->guid;
            $translation->disable();
        }
        
        $translation->save();
        $translation->save_draft($value);
        
        $this->set_content(json_encode(array(
            'guid' => $translation->guid
        )));    
    }
}