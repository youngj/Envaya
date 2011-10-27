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
 * must be registered with a unique string identifier (subtype_id) in the EntityRegistry. 
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

abstract class Entity extends Model
    implements Loggable, Serializable
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
    
    static $mixin_classes = array(
        'Mixin_Translatable',
    );
    
    function __construct($row = null)
    {
        parent::__construct($row);
        
        if ($row)
        {
            $this->cache_for_current_request();
        }
    }
    
    static function new_from_row($row)
    {
        if (isset(static::$table_attributes['subtype_id']))
        {    
            $cls = EntityRegistry::get_subtype_class($row->subtype_id);
            if (!$cls)
            {   
                throw new InvalidParameterException("Entity subtype {$row->subtype_id} is not defined");
            }                
            return new $cls($row);
        }
        else
        {
            return parent::new_from_row($row);
        }
    }
    
    static function get_subtype_id()
    {
        return EntityRegistry::get_subtype_id(get_called_class());
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
        get_cache()->delete(static::entity_cache_key($this->guid));
    }        
    
    function save_to_cache()
    {        
        $this->cache_for_current_request();
        get_cache()->set(static::entity_cache_key($this->guid), $this);
    }
    
    static function get_from_cache($guid)
    {
        if (isset(static::$current_request_entities[$guid]))
        {
            return static::$current_request_entities[$guid];
        }
        else
        {
            $entity = get_cache()->get(static::entity_cache_key($guid));
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
        return make_cache_key("entity", $guid);
    }  
    
    static function get_table_attributes()
    {
        $attributes = array_merge(
            parent::get_table_attributes(),
            array(
                'owner_guid' => 0,
                'container_guid' => 0,
                'time_created' => 0,
                'time_updated' => 0,
                'status' => Entity::Enabled
            )
        );
        
        if (isset($attributes['subtype_id']))
        {
            $attributes['subtype_id'] = static::get_subtype_id();
        }
        
        return $attributes;
    }    
    
    public function save_table_attributes()
    {
        $tableName = static::$table_name;
    
        $guid = $this->guid;
        if (Database::get_row("SELECT guid from $tableName where guid = ?", array($guid)))
        {
            Database::update_row($tableName, 'guid', $guid, $this->get_table_attribute_values());
        }
        else
        {
            $values = $this->get_table_attribute_values();
            $values['guid'] = $guid;                        
            Database::insert_row($tableName, $values);        
        }
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

    function can_view()
    {
        return $this->can_user_view(Session::get_loggedin_user());
    }
    
    function can_user_view($user)
    {
        if ($this->status == Entity::Disabled)
        {
            return false;
        }
    
        $container = $this->get_container_entity();
        if ($container && !$container->can_user_view($user))
        {
            return false;
        }
        
        return true;
    }
    
    function can_edit()
    {
        return $this->can_user_edit(Session::get_loggedin_user());
    }
    /**
     * Determines whether or not the specified user can edit the entity
     *
     * @param int $user The user
     * @return true|false
     */
    function can_user_edit($user)
    {
        if ($user)
        {
            if (($this->owner_guid == $user->guid)
             || ($this->container_guid == $user->guid)
             || ($this->guid == $user->guid)
             || $user->admin)
            {
                return true;
            }

            $container_entity = Entity::get_by_guid($this->container_guid);

            if ($container_entity && $container_entity->can_user_edit($user))
                return true;
        }
        return false;
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
        return abs_url("/{$this->guid}");
    }

    /**
     * Return a url for the entity's icon, trying multiple alternatives.
     *
     * @param string $size Either 'large','medium','small' or 'tiny'
     * @return string The url or false if no url could be worked out.
     */
    public function get_icon($size = 'medium')
    {
        return abs_url("/_media/images/default{$size}.gif");
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
        
        if ($guid == 0)
        {
            $this->guid = Database::insert_row('entities', array(
                'subtype_id' => static::get_subtype_id()
            ));
            
            if (!$this->guid)
                throw new IOException(__('error:BaseEntitySaveFailed'));
        }        
        $this->save_metadata();        
        $this->save_table_attributes();
        
        $this->clear_from_cache();
        $this->cache_for_current_request();
        
        if ($this->guess_language_field)
        {
            $this->queue_guess_language($this->guess_language_field);
            $this->guess_language_field = null;
        }
        
        EventRegister::trigger_event('update',get_class($this),$this);
    }

    function save_metadata()
    {
        foreach($this->metadata_cache as $name => $md)
        {
            if ($md->dirty)
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
        
        EventRegister::trigger_event('delete',get_class($this),$this);
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

    static function query()
    {
        $query_class = static::$query_class;
    
        $query = new $query_class(static::$table_name, get_called_class());
        
        if (isset(static::$query_subtype_ids))
        {            
            $query->where_in('subtype_id', static::$query_subtype_ids);
        }
        
        return $query;
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
        
        // avoid clobbering other changes to this entity
        $this->save_attribute_values(array(
            'language' => $this->language
        ));
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
            
            $next = $cur->get_container_entity();
            if ($cur == $next)
            {
                break;
            }                        
            $cur = $next;
        }
        return null;    
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
                    
    // Loggable interface
    public function get_id() { return $this->guid; }
    public function get_class_name() { return get_class($this); }
    static function get_object_from_id($id) { return Entity::get_by_guid($id); }    
}
