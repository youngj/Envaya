<?php

class Action_SaveTranslationDraft extends Action
{
    function before()
    {
        $this->set_content_type('text/javascript');    
        Permission_RegisteredUser::require_any();
    }

    function process_input()
    {        
        $value = Input::get_string('content');
        $key = $this->param('key');
        
        $user = Session::get_logged_in_user();
        
        $translation = $key->get_draft_translation_for_user($user);            
        if (!$translation)
        {
            $translation = $key->new_translation();
            $translation->source = Translation::Human;
            $translation->owner_guid = $user->guid;
            $translation->status = Translation::Draft;
        }
        
        $translation->save();
        $translation->save_draft($value);
        
        $this->set_content(json_encode(array(
            'guid' => $translation->guid
        )));    
    }
}