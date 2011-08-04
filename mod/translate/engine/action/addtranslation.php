<?php

class Action_AddTranslation extends Action
{
    function process_input()
    {
        $this->require_login();
        
        $value = get_input('value');
        
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
        
        $user = Session::get_loggedin_user();
                
        $translation = $key->get_draft_translation_for_user($user);                        
        if (!$translation)
        {                
            $translation = $key->new_translation();
            $translation->owner_guid = $user->guid;
        }
        $translation->value = $value;
        $translation->score = 1;
        
        if ($key->can_edit())
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