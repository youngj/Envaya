<?php

/*
 * Base class for many types of models. 
 *
 * Each Entity has a guid which is unique even among different entity subclasses.
 * This allows you to specify any subclass instance by guid, without needing to record the subclass separately.
 * This is kind of useful for things like feed items and translations, 
 * which may refer to many different types of entities. 
 * 
 * Entities also have an 'status' field which allows effectively deleting rows
 * while leaving them in the database to allow them to be undeleted.
 *
 * Entities can also have metadata, which allows storing/retreiving arbitrary properties 
 * (e.g. $entity->get_metadata('foo')) without needing to define them in the database schema.
 * 
 */

abstract class Entity extends Model implements Serializable
{
    // values for 'status' field
    const Disabled = 0; // aka 'deleted', except the db row still exists so we can undelete
    const Enabled = 1;  // not deleted    

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
                'owner_guid' => null,
                'container_guid' => null,
                'metadata_json' => null,
                'time_created' => 0,
                'time_updated' => 0,
                'status' => Entity::Enabled
            )
        );
    }    
    
    private $metadata;
    
    public function &get_metadata_object()
    {
        if (!isset($this->metadata))
        {
            $this->metadata = json_decode($this->metadata_json, true);
            if (!isset($this->metadata))
            {
                $this->metadata = array();
            }
        }
        return $this->metadata;
    }
    
    public function get_metadata($name)
    {
        $md = &$this->get_metadata_object();

        if (isset($md[$name]))
        {
            return $md[$name];
        }
        return null;
    }

    public function set_metadata($name, $value)
    {
        $md = &$this->get_metadata_object();
        
        if (isset($value))
        {
            $md[$name] = $value;            
        }
        else
        {
            unset($md[$name]);
        }
        
        $this->metadata_json = json_encode($md);         
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

    function get_guid()
    {
        $guid = $this->guid;
    
        if (!$guid)
        {
            $table_base_class = static::$table_base_class ?: get_class($this);
        
            $guid_prefix = PrefixRegistry::get_prefix($table_base_class);
        
            if (!$guid_prefix)
            {
                throw new Exception("$table_base_class does not have a registered guid prefix");
            }

            $this->guid = $guid = $guid_prefix . generate_random_code(22, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }
        return $guid;
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
        
        if (!$this->tid)
        {
            $values = $this->get_table_attribute_values();
            
            if (!isset($values['guid']))
            {
                $values['guid'] = $this->get_guid();
            }
            
            $this->attributes['tid'] = Database::insert_row($table_name, $values);
        }
        else
        {
            Database::update_row($table_name, 'guid', $guid, $this->get_dirty_attribute_values());        
        }

        $this->dirty_attributes = null;        
        $this->clear_from_cache();
        $this->cache_for_current_request();
        
        if ($this->guess_language_field)
        {
            $this->queue_guess_language($this->guess_language_field);
            $this->guess_language_field = null;
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
        $this->container_guid = $entity ? $entity->get_guid() : null;
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
            TaskQueue::queue_task(array(get_class($this), 'guess_language_by_guid'), array($this->guid, $field));
        }
        else
        {
            $this->guess_language_field = $field;
        }
    }
    
    static function guess_language_by_guid($guid, $field)
    {
        $entity = static::get_by_guid($guid);
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