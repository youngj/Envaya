<?php

class Action_AddInterfaceTranslation extends Action
{
    function process_input()
    {
        $this->require_login();
        
        $value = get_input('value');

        // don't allow people to sneak in bad HTML into translations
        $value = Markup::sanitize_html($value, array('HTML.AllowedElements' => 'em,strong,br'));        
        
        if ($value == '')
        {
            throw new ValidationException(__('itrans:empty'));
        }
                
        $key = $this->param('key');
        if (!$key->guid)
        {
            $key->save();
        }
        
        $placeholders = Language::get_placeholders($value);
        $correct_placeholders = $key->get_placeholders();
        sort($correct_placeholders);
        sort($placeholders);
        if ($correct_placeholders != $placeholders)
        {
            throw new ValidationException(__('itrans:placeholder_error'));
        }
        
        if ($key->query_translations()->where('value = ?', $value)->exists())
        {
            throw new ValidationException(__('itrans:duplicate'));
        }
        
        $user = Session::get_loggedin_user();
                
        $translation = new InterfaceTranslation();
        $translation->container_guid = $key->guid;
        $translation->owner_guid = $user->guid;
        $translation->value = $value;
        $translation->score = 1;
        $translation->save();
        
        $vote = new TranslationVote();
        $vote->container_guid = $translation->guid;
        $vote->owner_guid = $user->guid;
        $vote->score = 1;
        $vote->save();
        
        $key->update();
        $key->get_container_entity()->update();
        
        $language = $key->get_language();
        $language->get_stats_for_user($user)->update();
        
        SessionMessages::add(__('itrans:posted'));
        $this->redirect();
    }
}