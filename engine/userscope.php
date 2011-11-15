<?php
/*
 * UserScope is a container for User/Organization entities that
 * allows organizing users into a hierarchy, e.g.:
 *
 * UserScope (all users)
 *    UserScope (East Africa)
 *        UserScope (Tanzania)
 *             User1
 *             User2
 *             User3
 *        UserScope (Rwanda)
 *             User4
 *             User5
 *
 * UserScope entities allow creating permissions / subscriptions
 * that automatically include all users contained within that scope.
 * e.g. someone could subscribe to all users in Tanzania, or 
 * have admin permissions for all users in Rwanda. 
 */
class UserScope extends Entity
{
    static $table_name = 'user_scopes';
    
    static $admin_view = 'admin/entity/userscope';

    static $table_attributes = array(
        'description' => '',
        'order' => 0, // the order in which sibling scopes are matched by find_scope
        'filters_json' => '',
    );
    
    protected $filters;    
    
    function get_filters()
    {
        if (!isset($this->filters))
        {
            $this->filters = Query_Filter::json_decode_filters($this->filters_json);
        }
        return $this->filters;
    }
    
    function set_filters($filters)
    {
        $this->filters = $filters;
        $this->filters_json = Query_Filter::json_encode_filters($filters);
    }
    
    function get_title()
    {
        if (!$this->container_guid)
        {
            return 'All users';
        }
        else
        {
            return $this->description;
        }
    }
    
    function find_scope($user)
    {
        foreach ($this->query_scopes()->filter() as $child_scope)
        {   
            if (User::query()
                ->guid($user->guid)
                ->apply_filters($child_scope->get_filters())
                ->exists())
            {
                return $child_scope->find_scope($user);
            }
        }
        return $this;
    }
    
    function query_scopes()
    {
        return UserScope::query()
            ->where('container_guid = ?', $this->guid)
            ->order_by('`order`');
    }

    function query_users()
    {
        return User::query()->where('container_guid = ?', $this->guid);
    }    
    
	private static $root;
	
    static function get_root()
    {		
		if (!isset(static::$root))
		{
			$cache_key = make_cache_key('root_scope_guid');
			$cache = get_cache();
			$root_scope_guid = $cache->get($cache_key);
			if (!$root_scope_guid)
			{		
				$root = UserScope::query()->where('container_guid = 0')->get();
				if ($root)
				{				
					$cache->set($cache_key, $root->guid);
				}
			}
			else
			{
				$root = new UserScope();
				$root->guid = $root_scope_guid;
			}
			static::$root = $root;
		}
		return static::$root;
    }
}