<?php

abstract class Permission extends Entity
{    
    static $table_base_class = 'Permission';
    static $table_name = 'permissions';
    static $table_attributes = array(
        'subtype_id' => '',
        'args_json' => '',
    );            

    static $implicit = false;
    
    static function is_granted($entity, $user)
    {       
        if (!$user)
        {
            return false;
        }
        
        $user_guid = $user->guid;
        
        $cur = $entity;        
        
        $container_guids = array();        
        while ($cur)
        {        
            $guid = $cur->guid;
        
            if (static::$implicit)
            {
                if ($cur->owner_guid == $user_guid
                    || $cur->container_guid == $user_guid
                    || $cur->guid == $user_guid)
                {
                    return true;
                }
            }        
        
            $container_guids[] = $guid;
            $cur = $cur->get_container_entity();
        }        

        $cls = get_called_class();        
        foreach ($user->get_all_permissions() as $permission)
        {
            if (($permission instanceof $cls) && in_array($permission->container_guid, $container_guids))
            {
                return true;
            }
        }
        
        return false;
    }
    
    static function require_for_entity($entity)
    {        
        if (!static::has_for_entity($entity))
        {
            static::throw_exception();
        }
    }    

    static function throw_exception()
    {
        throw new PermissionDeniedException(Session::is_logged_in() ? __('page:noaccess') : '');
    }
    
    static function require_for_root()
    {
        static::require_for_entity(UserScope::get_root());
    }

    static function require_any()    
    {
        if (!static::has_any())
        {
            static::throw_exception();
        }
    }
    
    static function has_for_entity($entity)
    {
        return static::is_granted($entity, Session::get_logged_in_user());
    }
    
    static function has_for_root()
    {
        return static::has_for_entity(UserScope::get_root());
    }   

    static function has_any()
    {
        $user = Session::get_logged_in_user();    
        if (!$user)
        {
            return false;
        }
        
        $cls = get_called_class();        
        foreach ($user->get_all_permissions() as $permission)
        {
            if ($permission instanceof $cls)
            {
                return true;
            }
        }        
        return false;        
    }
}