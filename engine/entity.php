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

    static $primary_key = 'guid';    
    static $current_request_entities = array();
    
    function __construct($row = null)
    {
        parent::__construct($row);

        if ($row)
        {
            $this->cache_for_current_request();
        }
    }
    
    static function get_subtype_id()
    {
        return EntityRegistry::get_subtype_id(get_called_class());
    }

    public function get_date_text()
    {
        return friendly_time($this->time_created);
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

    /**
     * Return the value of a given key.
     * If $name is a key field (as defined in $this->attributes) that value is returned, otherwise it will
     * then look to see if the value is in this object's metadata.
     *
     * Q: Why are we not using __get overload here?
     * A: Because overload operators cause problems during subclassing, so we put the code here and
     * create overloads in subclasses.
     *
     * @param string $name
     * @return mixed Returns the value of a given value, or null.
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->attributes))
        {
            return $this->attributes[$name];
        }
        
        return $this->get_metadata($name);
    }

    /**
     * Set the value of a given key, replacing it if necessary.
     * If $name is a base attribute (as defined in $this->attributes) that value is set, otherwise it will
     * set the appropriate item of metadata.
     *
     * Note: It is important that your class populates $this->attributes with keys for all base attributes, anything
     * not in there gets set as METADATA.
     *
     * Q: Why are we not using __set overload here?
     * A: Because overload operators cause problems during subclassing, so we put the code here and
     * create overloads in subclasses.
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->attributes))
        {
            $this->attributes[$name] = $value;
        }
        else
        {
            $this->set_metadata($name, $value);
        }
        $this->dirty = true;
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
        if (!is_null($user))
        {
            if (($this->owner_guid == $user->guid)
             || ($this->container_guid == $user->guid)
             || ($this->guid == $user->guid)
             || $user->admin)
            {
                return true;
            }

            $container_entity = Entity::get_by_guid($this->container_guid);

            if ($container_entity && $container_entity->can_edit())
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
        return User::get_by_guid($this->get('owner_guid')); 
    }
    
    public function get_title()
    {
        return __("item:".strtolower(get_class($this)));
    }

    public function get_language()
    {
        $language = @$this->attributes['language'];
        if ($language)
        {
            return $language;
        }
        $container = $this->get_container_entity();
        if ($container)
        {
            return $container->get_language();
        }
        else
        {
            return 'en';
        }
    }

    /**
     * Gets the display URL for this entity
     *
     * @return string The URL
     */
    public function get_url() {
        return null;
    }

    /**
     * Return a url for the entity's icon, trying multiple alternatives.
     *
     * @param string $size Either 'large','medium','small' or 'tiny'
     * @return string The url or false if no url could be worked out.
     */
    public function get_icon($size = 'medium')
    {
        return Config::get('url')."_graphics/default{$size}.gif";
    }

    /**
     * Save generic attributes to the entities table.
     */
    public function save()
    {
        $time = time();
        $this->time_updated = $time;

        if (!$this->time_created)
        {
            $this->time_created = $time;
        }        
        
        if ($this->container_guid == 0)
        {
            $this->container_guid = $this->owner_guid;
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
        
        trigger_event('update',get_class($this),$this);
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
        return $this->status == Entity::Enabled;
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
        
        trigger_event('delete',get_class($this),$this);
    }

    function get_container_entity()
    {
        return Entity::get_by_guid($this->container_guid, true);
    }

    function get_root_container_entity()
    {
        if ($this->container_guid)
        {
            $containerEntity = $this->get_container_entity();
            if ($containerEntity == null || $containerEntity->guid == $this->guid)
            {
                return $this;
            }
            else
            {
                return $containerEntity->get_root_container_entity();
            }
        }
        else
        {
            return $this;
        }
    }
        
    public function translate_field($field, $isHTML = false)
    {
        $text = trim($this->$field);
        if (!$text)
        {
            return '';
        }

        $origLang = $this->get_language();
        $viewLang = Language::get_current_code();        

        if ($origLang != $viewLang)
        {            
            $translateMode = TranslateMode::get_current();
            $translation = $this->lookup_translation($field, $origLang, $viewLang, $translateMode, $isHTML);
            
            trigger_event('translate',get_class($this), $translation);
            
            if ($translation->owner_guid)
            {
                $viewTranslation = ($translateMode > TranslateMode::None);
            }
            else
            {
                $viewTranslation = ($translateMode == TranslateMode::All);
            }

            if ($viewTranslation && $translation->id)
            {
                return $translation->value;
            }
            else
            {
                return $this->$field;
            }
        }

        return $text;
    }        
        
    function lookup_auto_translation($prop, $origLang, $viewLang, $isHTML)
    {        
        $guid = $this->guid;
    
        $autoTrans =  Translation::query()
            ->where('property=?', $prop)
            ->where('lang=?',$viewLang)
            ->where('container_guid=?',$guid)
            ->where('html=?', $isHTML ? 1 : 0)
            ->where('owner_guid = 0')
            ->get();             
    
        if ($autoTrans && !$autoTrans->is_stale())
        {        
            return $autoTrans;
        }
        else
        {
            $text = GoogleTranslate::get_auto_translation($this->$prop, $origLang, $viewLang);

            if ($text != null)
            {
                if (!$autoTrans)
                {
                    $autoTrans = new Translation();                    
                    $autoTrans->owner_guid = 0;
                    $autoTrans->container_guid = $this->guid;
                    $autoTrans->property = $prop;
                    $autoTrans->html = $isHTML;
                    $autoTrans->lang = $viewLang;
                }
                $autoTrans->value = $text;                
                $autoTrans->save();
                
                return $autoTrans;
            }
        }
    }

    function lookup_translation($prop, $origLang, $viewLang, $translateMode = TranslateMode::ManualOnly, $isHTML = false)
    {
        $guid = $this->guid;
        
        $humanTrans = Translation::query()
            ->where('property=?', $prop)
            ->where('lang=?',$viewLang)
            ->where('container_guid=?',$guid)
            ->where('html=?', $isHTML ? 1 : 0)
            ->where('owner_guid > 0')
            ->order_by('time_updated desc')
            ->get();                

        $doAutoTranslate = ($translateMode == TranslateMode::All);

        if ($doAutoTranslate && (!$humanTrans || $humanTrans->is_stale()))
        {
            $autoTrans = $this->lookup_auto_translation($prop, $origLang, $viewLang, $isHTML);
            if ($autoTrans)
            {
                return $autoTrans;
            }
        }
        
        if ($humanTrans)
        {
            return $humanTrans;            
        }
        else
        {        
            // return translation with empty value
            $tempTrans = new Translation();
            $tempTrans->owner_guid = 0;
            $tempTrans->container_guid = $this->guid;
            $tempTrans->property = $prop;
            $tempTrans->lang = $viewLang;
            $tempTrans->html = $isHTML;        
            return $tempTrans;
        }
    }    

    static function query()
    {
        return new Query_SelectEntity(static::$table_name, get_called_class());
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
    
    // Loggable interface
    public function get_id() { return $this->guid; }
    public function get_class_name() { return get_class($this); }
    static function get_object_from_id($id) { return Entity::get_by_guid($id); }    
}
