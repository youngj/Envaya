<?php

/*
 * Base class for permissions, which determines if a user has authorization 
 * to perform a particular action within Envaya. Each permission is a subclass
 * defined in the permissions/ directory.
 *
 * Permissions can be implicit (automatically inferred from an entity without 
 * needing an actual Permission instance) or explicit (determined from Permission 
 * instances stored in the database).
 *
 * Implicit permissions are granted if the user is the entity's owner or 
 * if the user is a parent/ancestor container for that entity 
 * (including if the user and entity are the same).
 *
 * Explicit permissions are determined by checking if the user has any permissions
 * of this type for the entity or any ancestor containers. 
 *
 * For example, a user that has Permission_EditUserSettings on UserScope::get_root() 
 * will be able to edit the settings for any user. A user with Permission_EditUserSettings
 * on a UserScope with filter Query_Filter_Country(array('value' => 'rw')) will only
 * be able to edit the settings for users in Rwanda.
 */
abstract class Permission extends Entity
{    
    static $table_base_class = 'Permission';
    static $table_name = 'permissions';
    static $table_attributes = array(
        'subtype_id' => '',
    );            

    /*
     * Is this permission is implicitly granted by owner_guid or container_guid?
     */
    static $implicit = false;    
    
    /*
     * Returns true if the given user has this permission on the given entity, false otherwise.
     */
    static function is_granted($entity, $user)
    {       
        if (!$user || !$entity)
        {
            return false;
        }
        
        if (static::$implicit && static::is_granted_implicit($entity, $user))
        {
            return true;
        }
        
        return static::get_explicit($entity, $user) != null;
    }
    
    static function is_granted_implicit($entity, $user)
    {
        $owner_guid = $entity->owner_guid;
        $user_guid = $user->guid;            
        if ($owner_guid == $user_guid)
        {
            return true;
        }
    
        $entity_user = $entity->get_container_user();
        if ($user->equals($entity_user))
        {
            return true;
        }    
        return false;
    }
    
    static function get_explicit($entity, $user)
    {
        $permissions = static::filter($user->get_all_permissions());                
        if ($permissions)
        {
            $cur = $entity;
            $container_guids = array();        
            while ($cur)
            {                    
                $container_guids[] = $cur->guid;
                $cur = $cur->get_container_entity();
            }        
            
            foreach ($permissions as $permission)
            {
                if (in_array($permission->container_guid, $container_guids))
                {
                    return $permission;
                }
            }
        }        
        return null;    
    }
    
    protected static function filter($permissions)
    {
        $cls = get_called_class();
        $res = array();
        foreach ($permissions as $permission)
        {        
            if ($permission instanceof $cls)
            {
                $res[] = $permission;
            }
        }
        return $res;
    }
    
    static function require_for_entity($entity)
    {        
        if (!static::has_for_entity($entity))
        {
            static::throw_exception($entity);
        }
    }    

    static function throw_exception($entity = null)
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
        return static::is_any_granted(Session::get_logged_in_user());    
    }
    
    static function is_any_granted($user)
    {
        return static::get_any_explicit($user) != null;
    }
    
    static function get_any_explicit($user)
    {
        if (!$user)
        {
            return null;
        }    
    
        $cls = get_called_class();        
        foreach ($user->get_all_permissions() as $permission)
        {
            if ($permission instanceof $cls)
            {
                return $permission;
            }
        }        
        return null;       
    }
	
	static function get_all_explicit($user)
	{
		$permissions = array();
		if ($user)
        {            
			$cls = get_called_class();        
			foreach ($user->get_all_permissions() as $permission)
			{
				if ($permission instanceof $cls)
				{
					$permissions[] = $permission;
				}
			}     
		}
        return $permissions;    
	}
    
    static function grant_explicit($entity, $user)
    {
        $permission = static::get_explicit($entity, $user);
        
        if (!$permission)
        {    
            $cls = get_called_class();
            
            $permission = new $cls();
            $permission->set_owner_entity($user);
            $permission->set_container_entity($entity);
            $permission->save();
        }
        return $permission;
    }
    
    static function get_max_password_age()
    {
        return null;
    }
    
    static function get_min_password_strength()
    {
        return PasswordStrength::Average;
    }    
    
    function __toString()
    {
        $user = $this->get_owner_entity();
        $type = $this->get_subtype_id();
        return "{$this->guid}: type={$type} scope={$this->container_guid} username={$user->username}";
    }
    
    static function filter_for_entity($entity)
    {
        $cur = $entity;
        $permissions = array();
        
        while ($cur != null)
        {
            foreach (static::query_for_entity($cur)->filter() as $permission)
            {
                $permissions[] = $permission;
            }            
            $cur = $cur->get_container_entity();
        }        
        return $permissions;
    }
    
    static function query_for_entity($entity)
    {
        return static::query()->where('container_guid = ?', $entity->guid);
    }
    
    function get_title()
    {
        return str_replace('Permission_', '', get_class($this));
    }
}