<?php

/**
 * ElggEntity The elgg entity superclass
 * This class holds methods for accessing the main entities table.
 *
 * @author Curverider Ltd <info@elgg.com>
 * @package Elgg
 * @subpackage Core
 */
abstract class ElggEntity implements
    Loggable,   // Can events related to this object class be logged
    Serializable
{
    /**
     * The main attributes of an entity.
     * Blank entries for all database fields should be created by the constructor.
     * Subclasses should add to this in their constructors.
     * Any field not appearing in this will be viewed as metadata
     */
    protected $attributes;

    protected $metadata_cache;

    protected $table_attribute_names;

    static $subtype_id = 0;

    function __construct($guid = null)
    {
        $this->initialise_attributes();

        if (!empty($guid))
        {
            if ($guid instanceof stdClass) // either a entity row, or a user table row.
            {
                if (!$this->loadFromPartialTableRow($guid))
                {
                    throw new IOException(sprintf(__('error:FailedToLoadGUID'), get_class(), $guid->guid));
                }
            }
            else if ($guid instanceof ElggEntity)
            {
                foreach ($guid->attributes as $key => $value)
                    $this->attributes[$key] = $value;
            }
            else if (is_numeric($guid))
            {
                if (!$this->load($guid))
                    throw new IOException(sprintf(__('error:FailedToLoadGUID'), get_class(), $guid));
            }
            else
            {
                throw new InvalidParameterException(__('error:UnrecognisedValue'));
            }
        }
    }

    public function serialize()
    {
        return serialize($this->attributes);
    }

    public function unserialize($data)
    {
        $this->initialise_attributes();
        $this->attributes = unserialize($data);
    }

    protected function loadFromPartialTableRow($row)
    {
        $entityRow = (property_exists($row, 'type')) ? $row : get_entity_as_row($row->guid);
        return $this->loadFromTableRow($entityRow);
    }

    /**
     * Initialise the attributes array.
     * This is vital to distinguish between metadata and base parameters.
     *
     * Place your base parameters here.
     *
     * @return void
     */
    protected function initialise_attributes()
    {
        if (!is_array($this->attributes))
            $this->attributes = array();

        if (!is_array($this->metadata_cache))
            $this->metadata_cache = array();

        $this->attributes['guid'] = "";
        $this->attributes['type'] = "";
        $this->attributes['subtype'] = 0;

        $this->attributes['owner_guid'] = 0;
        $this->attributes['container_guid'] = 0;

        $this->attributes['site_guid'] = 0;
        $this->attributes['time_created'] = "";
        $this->attributes['time_updated'] = "";
        $this->attributes['enabled'] = "yes";
    }

    protected function initializeTableAttributes($tableName, $arr)
    {
        $tableAttributes = array();
        foreach ($arr as $name => $default)
        {
            $tableAttributes[] = $name;
            $this->attributes[$name] = $default;
        }

        if (!is_array($this->table_attribute_names))
        {
            $this->table_attribute_names = array();
        }

        $this->table_attribute_names[$tableName] = $tableAttributes;
    }

    protected function getTableAttributes($tableName)
    {
        $tableAttributes = array();
        foreach ($this->table_attribute_names[$tableName] as $name)
        {
            $tableAttributes[$name] = $this->attributes[$name];
        }
        return $tableAttributes;
    }

    public function saveTableAttributes($tableName)
    {
        $guid = $this->guid;
        if (get_data_row("SELECT guid from $tableName where guid = ?", array($guid)))
        {
            $args = array();
            $set = array();
            foreach ($this->getTableAttributes($tableName) as $name => $value)
            {
                $set[] = "`$name` = ?";
                $args[] = $value;
            }

            $args[] = $guid;

            return update_data("UPDATE $tableName set ".implode(',', $set)." where guid = ?", $args);
        }
        else
        {
            $columns = array('guid');
            $questions = array('?');
            $args = array($guid);

            foreach ($this->getTableAttributes($tableName) as $name => $value)
            {
                $columns[] = "`$name`";
                $questions[] = '?';
                $args[] = $value;
            }

            return update_data("INSERT into $tableName (".implode(',', $columns).") values (".implode(',', $questions).")", $args);
        }
    }

    public function deleteTableAttributes($tableName)
    {
        delete_data("DELETE from $tableName where guid=?", array($this->guid));
        return true;
    }

    public function selectTableAttributes($tableName, $guid)
    {
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

        // No, so see if its in the meta data for this entity
        $meta = $this->getMetaData($name);
        if ($meta)
            return $meta;

        // Can't find it, so return null
        return null;
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
            // Check that we're not trying to change the guid!
            if ((array_key_exists('guid', $this->attributes)) && ($name=='guid'))
                return false;

            $this->attributes[$name] = $value;
        }
        else
        {
            return $this->setMetaData($name, $value);
        }

        return true;
    }

    public function getMetaData($name)
    {
        $md = $this->getMetaDataObject($name);

        if ($md)
        {
            return $md->value;
        }
        return null;
    }

    protected function getMetaDataObject($name)
    {
        if (isset($this->metadata_cache[$name]))
        {
            return $this->metadata_cache[$name];
        }

        $md = null;

        if ((int) ($this->guid) > 0)
        {
            $md = get_metadata_byname($this->getGUID(), $name);
        }

        if (!$md)
        {
            $md = new ElggMetadata();
            $md->entity_guid = $this->guid;
            $md->name = $name;
            $md->value = null;
            $md->owner_guid = $this->owner_guid;
        }

        $this->metadata_cache[$name] = $md;
        return $md;
    }

    /**
     * Class member get overloading
     *
     * @param string $name
     * @return mixed
     */
    function __get($name) { return $this->get($name); }

    /**
     * Class member set overloading
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    function __set($name, $value) { return $this->set($name, $value); }

    /**
     * Supporting isset.
     *
     * @param string $name The name of the attribute or metadata.
     * @return bool
     */
    function __isset($name) { if ($this->$name!="") return true; else return false; }

    /**
     * Supporting unsetting of magic attributes.
     *
     * @param string $name The name of the attribute or metadata.
     */
    function __unset($name)
    {
        if (array_key_exists($name, $this->attributes))
        {
            $this->attributes[$name] = "";
        }
        else
        {
            $this->setMetaData($name, null);
        }
    }

    public function setMetaData($name, $value)
    {
        $md = $this->getMetaDataObject($name);
        $md->value = $value;
        $md->dirty = true;
        return true;
    }

    public function clearMetaData()
    {
        return delete_data("DELETE from metadata where entity_guid=?", array($this->getGUID()));
    }
    
    public function getSubEntities()
    {
        $guid = $this->guid;
        return array_map('entity_row_to_elggstar',
            get_data("SELECT * from entities WHERE container_guid=? or owner_guid=? or site_guid=?", array($guid, $guid, $guid))
        );
    }

    /**
     * Determines whether or not the specified user (by default the current one) can edit the entity
     *
     * @param int $user_guid The user GUID, optionally (defaults to the currently logged in user)
     * @return true|false
     */
    function canEdit($user_guid = 0)
    {
        $user_guid = (int)$user_guid;
        $user = get_entity($user_guid);

        if (!$user)
            $user = get_loggedin_user();

        // Test user if possible - should default to false unless a plugin hook says otherwise
        if (!is_null($user))
        {
            if (($this->getOwner() == $user->getGUID())
                || ($this->container_guid == $user->getGUID())
                || ($this->type == "user" && $this->getGUID() == $user->getGUID())
                || $user->admin)
            {
                return true;
            }

            $container_entity = get_entity($this->container_guid);

            if ($container_entity && $container_entity->canEdit())
                return true;
        }
        return false;
    }

    /**
     * Obtain this entity's GUID
     *
     * @return int GUID
     */
    public function getGUID() { return $this->get('guid'); }

    /**
     * Get the owner of this entity
     *
     * @return int The owner GUID
     */
    public function getOwner() { return $this->get('owner_guid'); }

    /**
     * Returns the actual entity of the user who owns this entity, if any
     *
     * @return ElggEntity The owning user
     */
    public function getOwnerEntity() { return get_entity($this->get('owner_guid')); }

    /**
     * Gets the type of entity this is
     *
     * @return string Entity type
     */
    public function getType() { return $this->get('type'); }

    /**
     * Returns the subtype of this entity
     *
     * @return string The entity subtype
     */
    public function getSubtype() {
        return $this->get('subtype');
    }

    public function getSubtypeName()
    {
        return get_subtype_from_id($this->get('subtype'));
    }

    public function getTitle()
    {
        return __("item:{$this->type}:{$this->getSubtypeName()}");
    }

    public function getLanguage()
    {
        $language = @$this->attributes['language'];
        if ($language)
        {
            return $language;
        }
        $container = $this->getContainerEntity();
        if ($container)
        {
            return $container->getLanguage();
        }
    }

    /**
     * Gets the display URL for this entity
     *
     * @return string The URL
     */
    public function getURL() {
        return null;
    }

    /**
     * Return a url for the entity's icon, trying multiple alternatives.
     *
     * @param string $size Either 'large','medium','small' or 'tiny'
     * @return string The url or false if no url could be worked out.
     */
    public function getIcon($size = 'medium')
    {
        global $CONFIG;
        return "{$CONFIG->url}_graphics/default{$size}.gif";
    }

    /**
     * Save generic attributes to the entities table.
     */
    public function save()
    {
        $guid = (int) $this->guid;
        if ($guid > 0)
        {
            if (trigger_event('update',$this->type,$this))
            {
                $time = time();
                $this->time_updated = $time;

                $res = update_data("UPDATE entities set owner_guid=?, container_guid=?, enabled=?, time_updated=? WHERE guid=?",
                    array($this->owner_guid,$this->container_guid,$this->enabled,$this->time_updated,$guid)
                );
                cache_entity($this);
            }
        }
        else
        {
            $time = time();

            if ($this->container_guid == 0)
                $this->container_guid = $this->owner_guid;

            if ($this->type == "")
                throw new InvalidParameterException(__('error:EntityTypeNotSet'));

            $this->time_created = $time;
            $this->time_updated = $time;

            $this->attributes['guid'] = insert_data("INSERT into entities (type, subtype, owner_guid, site_guid, container_guid, enabled,  time_created, time_updated) values (?,?,?,?,?,?,?,?)",
                array($this->type, $this->subtype, $this->owner_guid, $this->site_guid,
                    $this->container_guid, $this->enabled, $this->time_created, $this->time_updated)
            );

            if (!$this->guid)
                throw new IOException(__('error:BaseEntitySaveFailed'));

            if ($this->guid)
                cache_entity($this);

            $res = true;
        }

        $this->saveMetaData();

        return $res;
    }

    function saveMetaData()
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
                $md->dirty = false;
            }
        }

    }

    /**
     * Load the basic entity information and populate base attributes array.
     *
     * @param int $guid
     */
    protected function load($guid)
    {
        $row = get_entity_as_row($guid);

        if ($row)
        {
            $this->loadFromTableRow($row);
            return true;
        }

        return false;
    }

    protected function loadFromTableRow($row)
    {
        $typeBefore = $this->attributes['type'];

        $objarray = (array) $row;

        foreach($objarray as $key => $value)
            $this->attributes[$key] = $value;

        if ($this->attributes['type'] != $typeBefore)
            throw new InvalidClassException(sprintf(__('error:NotValidElggStar'), $guid, get_class()));

        global $ENTITY_CACHE;
        $ENTITY_CACHE[$this->guid] = $this;

        return true;
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
    public function isEnabled()
    {
        return ($this->enabled == 'yes');
    }

    /**
     * Delete this entity.
     */
    public function delete()
    {
        if (trigger_event('delete',$this->type,$this))
        {
            $sub_entities = $this->getSubEntities();
            if ($sub_entities)
            {
                foreach ($sub_entities as $e)
                    $e->delete();
            }

            $this->clearMetaData();

            $res = delete_data("DELETE from entities where guid=?", array($this->guid));

            invalidate_cache_for_entity($this->guid);

            return $res;
        }
        return false;
    }

    function getContainerEntity()
    {
        return get_entity($this->container_guid);
    }

    function getRootContainerEntity()
    {
        if ($this->container_guid)
        {
            $containerEntity = $this->getContainerEntity();
            if ($containerEntity == null || $containerEntity->guid == $this->guid)
            {
                return $this;
            }
            else
            {
                return $containerEntity->getRootContainerEntity();
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

        $origLang = $this->getLanguage();
        $viewLang = get_language();
                
        if ($origLang != $viewLang)
        {            
            $translateMode = get_translate_mode();
            $translation = $this->lookup_translation($field, $origLang, $viewLang, $translateMode, $isHTML);
            
            trigger_event('translate',$this->type, $translation);
            
            if ($translation->guid && $translation->owner_guid)
            {
                $viewTranslation = ($translateMode > TranslateMode::None);
            }
            else
            {
                $viewTranslation = ($translateMode == TranslateMode::All);
            }

            if ($viewTranslation && $translation->guid)
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

    function lookup_translation($prop, $origLang, $viewLang, $translateMode = TranslateMode::ManualOnly, $isHTML = false)
    {
        $trans = Translation::query()->where('property=?', $prop)->where('lang=?',$viewLang)->
                    where('container_guid=?',$this->guid)->where('html=?', $isHTML ? 1 : 0)->get();

        $doAutoTranslate = ($translateMode == TranslateMode::All);

        if ($trans)
        {
            if ($doAutoTranslate && $trans->isStale())
            {
                $text = get_auto_translation($this->$prop, $origLang, $viewLang);
                if ($text != null)
                {
                    if (!$trans->owner_guid) // previous version was from google
                    {
                        $trans->value = $text;
                        $trans->save();
                    }
                    else // previous version was from human
                    {
                        // TODO : cache this
                        $fakeTrans = new Translation();
                        $fakeTrans->owner_guid = 0;
                        $fakeTrans->container_guid = $this->guid;
                        $fakeTrans->property = $prop;
                        $fakeTrans->lang = $viewLang;
                        $fakeTrans->value = $text;
                        $fakeTrans->html = $isHTML;
                        return $fakeTrans;
                    }
                }
            }

            return $trans;
        }
        else if ($doAutoTranslate)
        {
            $text = get_auto_translation($this->$prop, $origLang, $viewLang);

            if ($text != null)
            {
                $trans = new Translation();
                $trans->owner_guid = 0;
                $trans->container_guid = $this->guid;
                $trans->property = $prop;
                $trans->lang = $viewLang;
                $trans->value = $text;
                $trans->html = $isHTML;
                $trans->save();
                return $trans;
            }
        }
        
        /* not saved */
        $tempTrans = new Translation();
        $tempTrans->owner_guid = 0;
        $tempTrans->container_guid = $this->guid;
        $tempTrans->property = $prop;
        $tempTrans->lang = $viewLang;
        $tempTrans->html = $isHTML;        
        return $tempTrans;
    }    

    static function queryByMetadata($meta_name, $meta_value = "")
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
    
    // SYSTEM LOG INTERFACE ////////////////////////////////////////////////////////////

    /**
     * Return an identification for the object for storage in the system log.
     * This id must be an integer.
     *
     * @return int
     */
    public function getSystemLogID() { return $this->getGUID(); }

    /**
     * Return the class name of the object.
     */
    public function getClassName() { return get_class($this); }

    /**
     * For a given ID, return the object associated with it.
     * This is used by the river functionality primarily.
     * This is useful for checking access permissions etc on objects.
     */
    public function getObjectFromID($id) { return get_entity($id); }
    
}
