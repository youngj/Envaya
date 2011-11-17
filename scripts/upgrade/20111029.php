<?php

    require_once "start.php";      
    
    $root_scope = UserScope::get_root();

    $defaults = array(
        'owner_guid' => 0, 
        'language' => Config::get('language')
    );    
    $email = Config::get('mail:admin_email');    
    
    foreach (TranslationLanguage::query()->filter() as $language)
    {
        $language->set_container_entity($root_scope);
        $language->save();
    }    
    
    Database::delete("DELETE from email_subscriptions WHERE email = ? AND container_guid <> ?", array($email, $root_scope->guid));
    
    EmailSubscription_Comments::init_for_entity($root_scope, $email, $defaults);
    EmailSubscription_Discussion::init_for_entity($root_scope, $email, $defaults);
    
    
    