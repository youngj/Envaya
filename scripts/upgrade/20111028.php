<?php
    require_once "start.php";      
    
    $root_scope = UserScope::query()->where('container_guid = 0')->get();
    if (!$root_scope)
    {    
        $root_scope = new UserScope();
        $root_scope->save();
    }
    
    $country_scopes = array(
        'Tanzania' => array(
            new Query_Filter_Country(array('value' => 'tz'))
        ),
        'Rwanda' => array(
            new Query_Filter_Country(array('value' => 'rw'))
        ),
        'Liberia' => array(
            new Query_Filter_Country(array('value' => 'lr'))
        ),
        'United States' => array(
            new Query_Filter_Country(array('value' => 'us'))
        ),
    );
    
    foreach ($country_scopes as $country_name => $filters)
    {
        $country_scope = $root_scope->query_scopes()->where('description = ?', $country_name)->get();
        if (!$country_scope)
        {
            $country_scope = new UserScope();
            $country_scope->description = $country_name;
            $country_scope->set_container_entity($root_scope);
            $country_scope->set_filters($filters);        
            $country_scope->save();
        }
        $country = $filters[0]->value;
        
        Database::update("update users set container_guid = ? where country = ?", array($country_scope->guid, $country));        
    }    
    
    $defaults = array(
        'owner_guid' => 0, 
        'language' => Config::get('language')
    );    
    $email = Config::get('admin_email');    
    
    Database::delete("DELETE from email_subscriptions WHERE email = ? AND container_guid <> ?", array($email, $root_scope->guid));
    
    EmailSubscription_Comments::init_for_entity($root_scope, $email, $defaults);
    EmailSubscription_Discussion::init_for_entity($root_scope, $email, $defaults);