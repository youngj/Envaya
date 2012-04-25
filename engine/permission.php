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
 * on a UserScope with filter Query_Filter_User_Country(array('value' => 'rw')) will only
 * be able to edit the settings for users in Rwanda.
 */
abstract class Permission extends Entity
{    
    static $table_base_class = 'Permission';
    static $table_name = 'permissions';
    static $table_attributes = array(
        'subtype_id' => '',
        'flags' => 0,
    );            

    const HighSecurity = 1;
    const Logged = 2;
    
    /*
     * Is this permission is implicitly granted by owner_guid or container_guid?
     */
    static $implicit = false;    
    
    static $require_passed = false;
       
    // subclasses may override to provide different rules
    static function get_for_current_user($entity)
    {
        return static::get($entity, Session::get_logged_in_user());
    }     
     
    // subclasses may override to provide different rules
    static function get($entity, $user)
    {       
        if (!$user || !$entity)
        {
            return null;
        }
        
        if (static::$implicit && static::is_granted_implicit($entity, $user))
        {
            return new Permission_Implicit();
        }
        
        return static::get_explicit($entity, $user);
    }
       
    /*
     * Returns true if the given user has this permission on the given entity, false otherwise.
     * Subclasses should not override.
     */
    static function is_granted($entity, $user)
    {       
        return static::get($entity, $user) != null;
    }
     
    static function is_granted_implicit($entity, $user)
    {
        return $user->equals($entity->get_container_user());
    }
    
    static function get_explicit($entity, $user)
    {
        $permissions = static::filter($user->get_all_permissions());                
        if ($permissions)
        {
            $cur = $entity;
            while ($cur)
            {                                    
                foreach ($permissions as $permission)
                {
                    if ($permission->container_guid == $cur->guid)
                    {
                        return $permission;
                    }
                }
                
                $cur = $cur->get_container_entity();
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
        $permission = static::get_for_current_user($entity);
    
        if (!$permission)
        {
            static::throw_exception($entity);
        }        
        else
        {
            $permission->handle_require($entity);
        }
    }   
    
    function check_session_security()
    {
        if ($this->is_high_security())
        {
            if (!Session::is_high_security())
            {
                throw new PermissionDeniedException(__('login:expired'));
            }
        }
        else
        {            
            if (Session::is_logged_in() && !Session::is_medium_security())
            {
                throw new PermissionDeniedException(__('login:expired'));
            }
        }    
    }
    
    function handle_require($entity)
    {
        $this->check_session_security();   
        
        if ($this->is_logged())
        {
            LogEntry::create("permission:used", $entity, $this->get_title());
        }
        
        self::$require_passed = true;    
    }
    
    function is_high_security()
    {
        return ($this->flags & self::HighSecurity) != 0;
    }

    function is_logged()
    {
        return ($this->flags & self::Logged) != 0;
    }    
    
    static function require_passed()
    {
        return self::$require_passed;
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
        $permission = static::get_any_explicit(Session::get_logged_in_user());
    
        if (!$permission)
        {
            static::throw_exception();
        }        
        else
        {
            $permission->handle_require(null);
        }
    }
    
    static function has_for_entity($entity)
    {
        return static::get_for_current_user($entity) != null;
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
    
    static function grant_explicit($entity, $user, $flags = 0)
    {
        $permission = static::get_explicit($entity, $user);
        
        if (!$permission)
        {    
            $cls = get_called_class();
            
            $permission = new $cls();
            $permission->flags = $flags;
            $permission->set_owner_entity($user);
            $permission->set_container_entity($entity);
            $permission->save();
        }
        else if ($permission->flags != $flags)
        {
            $permission->flags = $flags;
            $permission->save();
        }
        
        LogEntry::create('permission:grant', $entity, "{$user->email} / {$permission->get_title()}");
        
        return $permission;
    }
    
    static function get_max_password_age()
    {
        return null;
    }
    
    static function get_min_password_strength()
    {
        return PasswordStrength::Weak;
    }    
    
    function __toString()
    {
        $user = $this->get_owner_entity();
        $type = $this->get_subtype_id();
        return "{$this->guid}: type={$type} scope={$this->container_guid} username={$user->username} flags={$this->flags}";
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
        $title = str_replace('Permission_', '', get_class($this));
        
        if ($this->is_high_security())
        {
            $title .= "$";
        }
        if ($this->is_logged())
        {
            $title .= "+";
        }
        return $title;
    }
    
    static function get_type_description()
    {
        return get_called_class();
    }
}