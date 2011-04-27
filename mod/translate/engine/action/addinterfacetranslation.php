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
        
        $duplicate = $key->query_translations()->with_metadata('uniqid', get_input('uniqid'))->get();
        if ($duplicate)
        {
            forward($key->get_url());
        }
        
        $user = Session::get_loggedin_user();
                
        $translation = new InterfaceTranslation();
        $translation->container_guid = $key->guid;
        $translation->owner_guid = $user->guid;
        $translation->value = $value;
        $translation->save();
        $key->update();
        $key->get_container_entity()->update();
        
        SessionMessages::add(__('itrans:posted'));
        forward($key->get_url());
    }
}