<?php

abstract class Entity extends Model
    implements Loggable, Serializable
{
    protected $metadata_cache = array();        

    static $primary_key = 'guid';    
    static $subtype_id = 0;

    static $current_request_entities = array();
    
    function __construct($row = null)
    {
        parent::__construct($row);

        if ($row)
        {
            $this->cache_for_current_request();
        }
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
    
    protected function init_from_row($row)
    {
        $entityRow = (property_exists($row, 'type')) ? $row : get_entity_as_row($row->guid);
        parent::init_from_row($entityRow);
            
        if (!property_exists($row, get_first_key(static::$table_attributes)))
        {
            $objectEntityRow = $this->select_table_attributes($row->guid);
            parent::init_from_row($objectEntityRow);
        }
    }

    protected function initialize_attributes()
    {        
        $this->attributes['type'] = "object";
        $this->attributes['subtype'] = static::$subtype_id;
        $this->attributes['owner_guid'] = 0;
        $this->attributes['container_guid'] = 0;
        $this->attributes['site_guid'] = 0;
        $this->attributes['time_created'] = 0;
        $this->attributes['time_updated'] = 0;
        $this->attributes['enabled'] = "yes";
        
        parent::initialize_attributes();
    }

    public function save_table_attributes()
    {
        $tableName = static::$table_name;
    
        $guid = $this->guid;
        if (get_data_row("SELECT guid from $tableName where guid = ?", array($guid)))
        {
            update_db_row($tableName, 'guid', $guid, $this->get_table_attributes());
        }
        else
        {
            $values = $this->get_table_attributes();
            $values['guid'] = $guid;
                        
            insert_db_row($tableName, $values);        
        }
    }

    public function select_table_attributes($guid)
    {
        $tableName = static::$table_name;
        return get_data_row("SELECT * from $tableName where guid=?", array($guid));
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
            $md = get_metadata_byname($this->guid, $name);
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
        return delete_data("DELETE from metadata where entity_guid=?", array($this->guid));
    }
    
    public function get_sub_entities()
    {
        $guid = $this->guid;
        return array_map('entity_row_to_entity',
            get_data("SELECT * from entities WHERE container_guid=? or owner_guid=?", array($guid, $guid))
        );
    }

    /**
     * Determines whether or not the specified user (by default the current one) can edit the entity
     *
     * @param int $user The user, optionally (defaults to the currently logged in user)
     * @return true|false
     */
    function can_edit($user = null)
    {
        if (!$user)
            $user = Session::get_loggedin_user();

        if (!is_null($user))
        {
            if (($this->owner_guid == $user->guid)
             || ($this->container_guid == $user->guid)
             || ($this->type == "user" && $this->guid == $user->guid)
             || $user->admin)
            {
                return true;
            }

            $container_entity = get_entity($this->container_guid);

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
    public function get_owner_entity() { return get_entity($this->get('owner_guid')); }
    
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
        
        $entity_values = array(
            'owner_guid' => $this->owner_guid,
            'container_guid' => $this->container_guid,
            'enabled' => $this->enabled,
            'site_guid' => 0,
            'time_updated' => $this->time_updated,
            'time_created' => $this->time_created,
            'type' => $this->type,
            'subtype' => $this->subtype,
        );
        
        $guid = $this->guid;
        
        if ($guid > 0)
        {
            update_db_row('entities', 'guid', $guid, $entity_values);
        }
        else
        {            
            $this->guid = insert_db_row('entities', $entity_values);
            if (!$this->guid)
                throw new IOException(__('error:BaseEntitySaveFailed'));
        }        
        $this->save_metadata();        
        $this->save_table_attributes();
        
        $this->clear_from_cache();
        $this->cache_for_current_request();
        
        trigger_event('update',$this->type,$this);
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

    /**
     * Disable this entity.
     */
    public function disable()
    {
        $this->enabled = 'no';
    }

    /**
     * Re-enable this entity.
     */
    public function enable()
    {
        $this->enabled = 'yes';
    }

    /**
     * Is this entity enabled?
     *
     * @return boolean
     */
    public function is_enabled()
    {
        return ($this->enabled == 'yes');
    }

    /**
     * Delete this entity.
     */
    public function delete()
    {
        $sub_entities = $this->get_sub_entities();
        if ($sub_entities)
        {
            foreach ($sub_entities as $e)
                $e->delete();
        }

        $this->clear_metadata();

        $res = delete_data("DELETE from entities where guid=?", array($this->guid));
                
        parent::delete();
        $this->clear_from_cache();
        
        trigger_event('delete',$this->type,$this);
    }

    function get_container_entity()
    {
        return get_entity($this->container_guid);
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
        
    public function translate_field($field, $isHTML = false, $viewLang = null)
    {
        $text = trim($this->$field);
        if (!$text)
        {
            return '';
        }

        $origLang = $this->get_language();
        if ($viewLang == null)
        {
            $viewLang = get_language();
        }
                
        if ($origLang != $viewLang)
        {            
            $translateMode = get_translate_mode();
            $translation = $this->lookup_translation($field, $origLang, $viewLang, $translateMode, $isHTML);
            
            trigger_event('translate',$this->type, $translation);
            
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

    public function set_content($content, $isHTML)
    {
        if ($isHTML)
        {
            $content = Markup::sanitize_html($content);
        }
        else
        {
            $content = view('output/longtext', array('value' => $content));
        }
        
        $this->content = $content;
        $this->set_data_type(DataType::HTML, true);

        if ($isHTML)
        {
            $thumbnailUrl = UploadedFile::get_thumbnail_url_from_html($content);

            $this->set_data_type(DataType::Image, $thumbnailUrl != null);
            $this->thumbnail_url = $thumbnailUrl;            
        }

        if (!$this->language)
        {            
            $this->language = GoogleTranslate::guess_language($this->content);
        }
    }

    public function render_content()
    {
        $isHTML = $this->has_data_type(DataType::HTML);

        $content = $this->translate_field('content', $isHTML);

        if ($isHTML)
        {
            $content = Markup::render_custom_tags($content);
        
            return $content; // html content should be sanitized when it is input!
        }
        else
        {
            return view('output/longtext', array('value' => $content));
        }
    }

    public function has_data_type($dataType)
    {
        return ($this->data_types & $dataType) != 0;
    }

    public function set_data_type($dataType, $val)
    {
        if ($val)
        {
            $this->data_types |= $dataType;
        }
        else
        {
            $this->data_types &= ~$dataType;
        }
    }        
    
    static function query()
    {
        $query = new Query_SelectEntity(static::$table_name);
        $query->where("type='object'");
        $query->where("subtype=?", static::$subtype_id);
        return $query;
    }

    static function query_by_metadata($meta_name, $meta_value = "")
    {
        $query = static::query();  
        $query->join('JOIN metadata m on e.guid = m.entity_guid');

        if ($meta_name!=="")
        {
            $query->where("m.name=?", $meta_name);
        }

        if ($meta_value!=="")
        {
            $query->where("m.value=?", $meta_value);
        }
        return $query;
    }
    
    // Loggable interface
    public function get_id() { return $this->guid; }
    public function get_class_name() { return get_class($this); }
    static function get_object_from_id($id) { return get_entity($id); }    
}
