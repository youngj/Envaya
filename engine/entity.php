<?php

/*
 * Base class for many types of models. 
 *
 * Each Entity has a guid which is unique even among different entity subclasses.
 * This allows you to specify any subclass instance by guid, without needing to record the subclass separately.
 * This is kind of useful for things like feed items and translations, 
 * which may refer to many different types of entities. 
 *
 * In order for the system to determine which type of entity a guid refers to, the entity class name
 * must be registered with a unique string identifier (subtype_id) in the ClassRegistry. 
 * The 'entities' database table stores a subtype_id for each entity guid.
 * 
 * Entities also have an 'status' field which allows effectively deleting rows
 * while leaving them in the database to allow them to be undeleted.
 *
 * Entities can also have metadata, which allows storing/retreiving arbitrary properties (e.g. $entity->foo)
 * without needing to define them in the database schema. Metadata is only fetched when requested.
 * Warning: if you forget to define an attribute, or make a typo, a property might be saved
 * as metadata accidentally.
 * 
 */

abstract class Entity extends Model implements Serializable
{
    // values for 'status' field
    const Disabled = 0; // aka 'deleted', except the db row still exists so we can undelete
    const Enabled = 1;  // not deleted    

    protected $metadata_cache = array();        

    static $query_class = 'Query_SelectEntity';
    static $primary_key = 'guid';    
    static $current_request_entities = array();
    static $admin_view = null;
    
    protected $guess_language_field;
    
    function __construct($row = null)
    {
        parent::__construct($row);
        
        if ($row)
        {
            $this->cache_for_current_request();
        }
    }    
    
    public function get_date_text($time = null)
    {
        return friendly_time($time ?: $this->time_created);
    }    
    
    function cache_for_current_request()
    {
        static::$current_request_entities[$this->guid] = $this;
    }
    
    function clear_from_cache()
    {        
        unset(static::$current_request_entities[$this->guid]);
        Cache::get_instance()->delete(static::entity_cache_key($this->guid));
    }        
    
    function save_to_cache()
    {        
        $this->cache_for_current_request();
        Cache::get_instance()->set(static::entity_cache_key($this->guid), $this);
    }
    
    static function get_from_cache($guid)
    {
        if (isset(static::$current_request_entities[$guid]))
        {
            return static::$current_request_entities[$guid];
        }
        else
        {
            $entity = Cache::get_instance()->get(static::entity_cache_key($guid));
            if ($entity)
            {
                static::$current_request_entities[$guid] = $entity;
                return $entity;
            }
        }
        return null;
    }    
    
    static function entity_cache_key($guid)
    {
        return Cache::make_key("entity", $guid);
    }  
    
    static function get_table_attributes()
    {
        return array_merge(
            parent::get_table_attributes(),
            array(
                'owner_guid' => 0,
                'container_guid' => 0,
                'time_created' => 0,
                'time_updated' => 0,
                'status' => Entity::Enabled
            )
        );
    }    
    
    public function get_metadata($name)
    {
        $md = $this->get_metadata_object($name);

        if ($md)
        {
            return $md->value;
        }
        return null;
    }

    protected function get_metadata_object($name)
    {
        if (isset($this->metadata_cache[$name]))
        {
            return $this->metadata_cache[$name];
        }

        $md = null;

        if ((int) ($this->guid) > 0)
        {
            $md = EntityMetadata::query()->where('entity_guid = ? and name = ?', $this->guid, $name)->get();
        }

        if (!$md)
        {
            $md = new EntityMetadata();
            $md->entity_guid = $this->guid;
            $md->name = $name;
            $md->value = null;
            $md->owner_guid = $this->owner_guid;
        }

        $this->metadata_cache[$name] = $md;
        return $md;
    }

    public function set_metadata($name, $value)
    {
        $md = $this->get_metadata_object($name);
        $md->value = $value;
        return true;
    }

    public function clear_metadata()
    {
        return Database::delete("DELETE from metadata where entity_guid=?", array($this->guid));
    }

    /**
     * Returns the actual entity of the user who owns this entity, if any
     *
     * @return Entity The owning user
     */
    public function get_owner_entity() 
    { 
        return User::get_by_guid($this->owner_guid); 
    }
    
    public function set_owner_entity($user)
    {
        $this->owner_guid = $user ? $user->guid : 0;
    }
    
    public function get_title()
    {
        return get_class($this)."({$this->guid})";
    }

    /**
     * Gets the display URL for this entity
     *
     * @return string The URL
     */
    public function get_url() {
        return null;
    }
    
    public function get_short_url()
    {
        return "/{$this->guid}";
    }

    /**
     * Return a url for the entity's icon, trying multiple alternatives.
     *
     * @param string $size Either 'large','medium','small' or 'tiny'
     * @return string The url or false if no url could be worked out.
     */
    public function get_icon($size = 'medium')
    {
        return "/_media/images/default{$size}.gif";
    }

    /**
     * Save generic attributes to the entities table.
     */
    public function save()
    {
        $time = timestamp();
        $this->time_updated = $time;

        if (!$this->time_created)
        {
            $this->time_created = $time;
        }        
                
        $guid = $this->guid;
		
		$table_name = static::$table_name;
        
        if ($guid == 0)
        {
            $guid = $this->guid = Database::insert_row('entities', array(
                'subtype_id' => static::get_subtype_id()
            ));
            
            if (!$guid)
			{
                throw new IOException(__('error:BaseEntitySaveFailed'));
			}
				
            $values = $this->get_table_attribute_values();
            $values['guid'] = $guid;
            Database::insert_row($table_name, $values);
        }
		else
		{
			Database::update_row($table_name, 'guid', $guid, $this->get_dirty_attribute_values());		
		}
		
        $this->save_metadata();
		
        $this->clear_from_cache();
        $this->cache_for_current_request();
        
        if ($this->guess_language_field)
        {
            $this->queue_guess_language($this->guess_language_field);
            $this->guess_language_field = null;
        }
    }

    function save_metadata()
    {
        foreach($this->metadata_cache as $name => $md)
        {
            if ($md->is_dirty())
            {
                if ($md->value === null)
                {
                    $md->delete();
                }
                else
                {
                    $md->entity_guid = $this->guid;
                    $md->save();
                }                
            }
        }
    }

    public function set_status($status)
    {
        $this->status = $status;
    }
    
    /**
     * Disable this entity.
     */
    public function disable()
    {
        $this->set_status(Entity::Disabled);
    }

    /**
     * Re-enable this entity.
     */
    public function enable()
    {
        $this->set_status(Entity::Enabled);
    }

    /**
     * Is this entity enabled?
     *
     * @return boolean
     */
    public function is_enabled()
    {
        return $this->guid && $this->status == Entity::Enabled;
    }

    /**
     * Delete this entity.
     */
    public function delete()
    {
        $this->clear_metadata();

        $res = Database::delete("DELETE from entities where guid=?", array($this->guid));
                
        parent::delete();
        $this->clear_from_cache();
    }

    protected $container_entity;
    
    function get_container_entity()
    {
        if (!$this->container_entity)
        {
            $this->container_entity = Entity::get_by_guid($this->container_guid, true);
        }
        return $this->container_entity;
    }
    
    function equals($other)
    {
        return $other && $other->guid == $this->guid;
    }
    
    function set_container_entity($entity)
    {
        $this->container_entity = $entity;
        $this->container_guid = $entity->guid;
    }
    
    function save_draft($content)
    {
        $revision = ContentRevision::get_recent_draft($this);
        $revision->time_updated = timestamp();
        $revision->content = Markup::sanitize_html($content);
        $revision->save();
    }    
    
    static function get_by_guid($guid, $show_disabled = false)
    {    
        $guid = (int)$guid;
        
        if (!$guid)
        {
            return null;
        }
    
        $entity = Entity::get_from_cache($guid);
                
        if (!$entity)
        {
            $entity = static::query()
                ->show_disabled($show_disabled)
                ->guid($guid)
                ->get();                     
        
            if (!$entity)
            {
                return null;
            }
            $entity->save_to_cache();
        }

        if (!$show_disabled && $entity->status == Entity::Disabled)
        {
            return null;
        }       
        
        $cls = get_called_class();
        if (!($entity instanceof $cls))
        {
            return null;
        }
        
        return $entity;
    }
    
    function queue_guess_language($field)
    {
        if ($this->guid)
        {            
            FunctionQueue::queue_call(array('Entity', 'guess_language_by_guid'), array($this->guid, $field));
        }
        else
        {
            $this->guess_language_field = $field;
        }
    }
    
    static function guess_language_by_guid($guid, $field)
    {
        $entity = Entity::get_by_guid($guid);
        if ($entity)
        {
            $entity->guess_language($field);
        }
    }
    
    private function guess_language($field)
    {       
        try
        {
            $this->language = GoogleTranslate::guess_language($this->$field);
        }
        catch (GoogleTranslateException $ex)
        {
            // if the user's default language is not supported by Google Translate,
            // assume that the text is in that language if Google Translate fails
        
            $user = $this->get_container_user();
            if ($user && $user->language && !GoogleTranslate::is_supported_language($user->language))
            {
                $this->language = $user->language;
            }
        }        
        $this->save();
        $this->clear_from_cache();
    }
    
    function get_container_user()
    {
        $cur = $this;
        
        while ($cur)
        {
            if ($cur instanceof User)
            {
                return $cur;
            }            
            $cur = $cur->get_container_entity();
        }
        return null;    
    }  

	function is_contained_in($container)
	{
		$cur = $this;        
        while ($cur)
        {
            if ($cur->equals($container))
            {
                return true;
            }            
            $cur = $cur->get_container_entity();          
        }
        return false; 	
	}
    
    function get_local_id()
    {
        $row = Database::get_row("SELECT * FROM local_ids where guid = ?", array($this->guid));
        if ($row != null)
        {
            return $row->local_id;
        }
        
        $user = $this->get_container_user();
        
        $max_row = Database::get_row("SELECT max(local_id) as max FROM local_ids where user_guid = ?", array($user->guid));
        
        $max_id = $max_row ? ((int)$max_row->max) : 0;
        
        for ($i = 1; $i < 10; $i++)
        {
            try
            {
                $local_id = $max_id + $i;
            
                Database::update("INSERT INTO local_ids (guid,user_guid,local_id) VALUES (?,?,?)",
                    array($this->guid, $user->guid, $local_id));
                    
                return $local_id;
            }
            catch (DatabaseException $ex)
            {
                // duplicate local_id? try next one
            }
        }
        return null;
    }    
    
    function get_admin_url()
    {
        return "/admin/entity/{$this->guid}";
    }
    
    static function get_view_permission()
    {
        return null;
    }        
    
    function render_property($property)
    {
        $res = Hook_RenderEntityProperty::trigger(array(
            'entity' => $this,
            'property' => $property,
            'value' => $this->$property
        ));        
        return $res['value'];
    }
}