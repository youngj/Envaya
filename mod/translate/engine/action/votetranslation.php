<?php

class Action_VoteTranslation extends Action
{
    function process_input()
    {
        $this->require_login();
            
        $key = $this->param('key');
        $translation = $this->param('translation');
        $language = $key->get_language();
        
        $user = Session::get_logged_in_user();
        
        $delta = (int)get_input('delta');
                
        $vote = $translation->query_votes()->where('owner_guid = ?', $user->guid)->get();
        if (!$vote)
        {
            $vote = new TranslationVote();
            $vote->container_guid = $translation->guid;
            $vote->owner_guid = $user->guid;
        }
        $new_score = $vote->score + $delta;
        
        if (!Permission_ManageLanguage::has_for_entity($language))
        {
            $new_score = min($new_score, 1);
            $new_score = max($new_score, -1);
        }
        
        $vote->score = $new_score;
        $vote->save();
        
        $translation->update(true);        
                
        $language->get_stats_for_user($user)->update();        
                
        $this->redirect();
    }
}