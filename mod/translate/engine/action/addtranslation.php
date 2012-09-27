<?php

class Action_AddTranslation extends Action
{
    function before()
    {
        Permission_RegisteredUser::require_any();
    }

    function process_input()
    {        
        $value = Input::get_string('value');
        
        if ($value == '')
        {
            throw new ValidationException(__('itrans:empty'));
        }
                
        $key = $this->param('key');
        if (!$key->guid)
        {
            $key->save();
        }        
        
        $value = $key->sanitize_value($value);        
        
        if ($key->query_translations()->where('value = ?', $value)->exists())
        {
            throw new ValidationException(__('itrans:duplicate'));
        }
        
        $user = Session::get_logged_in_user();
                
        $translation = $key->get_draft_translation_for_user($user);                        
        if (!$translation)
        {                
            $translation = $key->new_translation();
            $translation->source = Translation::Human;
            $translation->owner_guid = $user->guid;
        }
        $translation->value = $value;
        $translation->score = 1;
        
        if (Permission_EditTranslation::has_for_entity($key))
        {
            $translation->set_approved(true);
        }
        $translation->enable();
        $translation->save();
        
        $vote = new TranslationVote();
        $vote->container_guid = $translation->guid;
        $vote->owner_guid = $user->guid;
        $vote->score = 1;
        $vote->save();
        
        $key->update(true);
        
        $language = $key->get_language();
        $language->get_stats_for_user($user)->update();
        
        SessionMessages::add(__('itrans:posted'));
        $this->redirect($this->get_parent_controller()->get_matched_uri());
    }
}