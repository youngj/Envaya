<?php
    /**
     * Elgg entities.
     * Functions to manage all elgg entities (sites, collections, objects and users).
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd <info@elgg.com>

     * @link http://elgg.org/
     */

    /// Cache objects in order to minimise database access.
    $ENTITY_CACHE = array();

    /// Require the locatable interface TODO: Move this into start.php?
    require_once('location.php');

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
         * Any field not appearing in this will be viewed as a
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
                        throw new IOException(sprintf(elgg_echo('IOException:FailedToLoadGUID'), get_class(), $guid->guid));
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
                        throw new IOException(sprintf(elgg_echo('IOException:FailedToLoadGUID'), get_class(), $guid));
                }
                else
                {
                    throw new InvalidParameterException(elgg_echo('InvalidParameterException:UnrecognisedValue'));
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
            $this->attributes['access_id'] = ACCESS_PRIVATE;
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
                $md->access_id = $this->access_id;
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

        public function clearMetaData($name = "")
        {
            if (empty($name)) {
                return clear_metadata($this->getGUID());
            } else {
                return remove_metadata($this->getGUID(),$name);
            }
        }

        public function getSubEntities()
        {
            $guid = $this->guid;
            return array_map('entity_row_to_elggstar',
                get_data("SELECT * from entities WHERE container_guid=? or owner_guid=? or site_guid=?", array($guid, $guid, $guid))
            );
        }

        function setPrivateSetting($name, $value) {
            return set_private_setting($this->getGUID(), $name, $value);
        }

        function getPrivateSetting($name) {
            return get_private_setting($this->getGUID(), $name);
        }

        function removePrivateSetting($name) {
            return remove_private_setting($this->getGUID(), $name);
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
         * Obtain this entity's access ID
         *
         * @return int The access ID
         */
        public function getAccessID() { return $this->get('access_id'); }

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
            return elgg_echo("item:{$this->type}:{$this->getSubtypeName()}");
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
         * Gets the UNIX epoch time that this entity was created
         *
         * @return int UNIX epoch time
         */
        public function getTimeCreated() { return $this->get('time_created'); }

        /**
         * Gets the UNIX epoch time that this entity was last updated
         *
         * @return int UNIX epoch time
         */
        public function getTimeUpdated() { return $this->get('time_updated'); }

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
                if (trigger_elgg_event('update',$this->type,$this))
                {
                    $time = time();
                    $this->time_updated = $time;

                    $res = update_data("UPDATE entities set owner_guid=?, access_id=?, container_guid=?, enabled=?, time_updated=? WHERE guid=?",
                        array($this->owner_guid,$this->access_id,$this->container_guid,$this->enabled,$this->time_updated,$guid)
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
                    throw new InvalidParameterException(elgg_echo('InvalidParameterException:EntityTypeNotSet'));

                $this->time_created = $time;
                $this->time_updated = $time;

                $this->attributes['guid'] = insert_data("INSERT into entities (type, subtype, owner_guid, site_guid, container_guid, enabled, access_id, time_created, time_updated) values (?,?,?,?,?,?,?,?,?)",
                    array($this->type, $this->subtype, $this->owner_guid, $this->site_guid,
                        $this->container_guid, $this->enabled, $this->access_id, $this->time_created, $this->time_updated)
                );

                if (!$this->guid)
                    throw new IOException(elgg_echo('IOException:BaseEntitySaveFailed'));

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
                    $md->entity_guid = $this->guid;
                    $md->save();
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
                throw new InvalidClassException(sprintf(elgg_echo('InvalidClassException:NotValidElggStar'), $guid, get_class()));

            global $ENTITY_CACHE;
            $ENTITY_CACHE[$this->guid] = $this;

            return true;
        }

        /**
         * Disable this entity.
         *
         * @param string $reason Optional reason
         * @param bool $recursive Recursively disable all contained entities?
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
            if (trigger_elgg_event('delete',$this->type,$this))
            {
                $sub_entities = $this->getSubEntities();
                if ($sub_entities)
                {
                    foreach ($sub_entities as $e)
                        $e->delete();
                }

                $this->clearMetadata();

                delete_data("DELETE from private_settings where entity_guid = ?", array($this->guid));

                $res = delete_data("DELETE from entities where guid=?", array($this->guid));

                invalidate_cache_for_entity($this->guid);

                return $res;
            }
            return false;
        }

        /**
         * Set the container for this object.
         *
         * @param int $container_guid The ID of the container.
         * @return bool
         */
        function setContainer($container_guid)
        {
            $container_guid = (int)$container_guid;

            return $this->set('container_guid', $container_guid);
        }

        /**
         * Return the container GUID of this object.
         *
         * @return int
         */
        function getContainer()
        {
            return $this->get('container_guid');
        }

        /**
         * As getContainer(), but returns the whole entity.
         */
        function getContainerEntity()
        {
            return get_entity($this->getContainer());
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

        /**
         * Return the GUID of the owner of this object.
         */
        public function getObjectOwnerGUID() { return $this->owner_guid; }
    }

    /**
     * Invalidate this class' entry in the cache.
     *
     * @param int $guid The guid
     */
    function invalidate_cache_for_entity($guid)
    {
        global $ENTITY_CACHE;
        $guid = (int)$guid;
        unset($ENTITY_CACHE[$guid]);
        get_cache()->delete(entity_cache_key($guid));
    }

    function cache_entity(ElggEntity $entity)
    {
        global $ENTITY_CACHE;

        $guid = $entity->guid;
        $ENTITY_CACHE[$guid] = $entity;
        get_cache()->set(entity_cache_key($guid), $entity);
    }

    function entity_cache_key($guid)
    {
        return make_cache_key("entity3", $guid);
    }

    /**
     * Retrieve a entity from the cache.
     *
     * @param int $guid The guid
     */
    function retrieve_cached_entity($guid)
    {
        global $ENTITY_CACHE;

        $guid = (int)$guid;

        if (isset($ENTITY_CACHE[$guid]))
        {
            return $ENTITY_CACHE[$guid];
        }
        else
        {
            $entity = get_cache()->get(entity_cache_key($guid));
            if ($entity)
            {
                $ENTITY_CACHE[$guid] = $entity;
                return $entity;
            }
        }
        return null;
    }

    /**
     * Return the integer ID for a given subtype, or false.
     *
     * TODO: Move to a nicer place?
     *
     * @param string $type
     * @param string $subtype
     */
    function get_subtype_id($type, $subtype)
    {
        global $CONFIG;
        foreach ($CONFIG->subtypes as $id => $info)
        {
            if ($info[0] == $type && $info[1] == $subtype)
            {
                return $id;
            }
        }

        return 0;
    }

    /**
     * For a given subtype ID, return its identifier text.
     *
     * TODO: Move to a nicer place?
     *
     * @param int $subtype_id
     */
    function get_subtype_from_id($subtype_id)
    {
        global $CONFIG;
        if (isset($CONFIG->subtypes[$subtype_id]))
        {
            return $CONFIG->subtypes[$subtype_id][1];
        }

        return false;
    }

    /**
     * This function tests to see if a subtype has a registered class handler by its id.
     *
     * @param int $subtype_id The subtype
     * @return a class name or null
     */
    function get_subtype_class($type, $subtype_id)
    {
        global $CONFIG;
        if (isset($CONFIG->subtypes[$subtype_id]))
        {
            return $CONFIG->subtypes[$subtype_id][2];
        }

        if (isset($CONFIG->types[$type]))
        {
            return $CONFIG->types[$type];
        }
        return NULL;
    }

    /**
     * Retrieve the entity details for a specific GUID, returning it as a stdClass db row.
     *
     * @param int $guid The GUID of the object to extract
     */
    function get_entity_as_row($guid)
    {
        global $CONFIG;

        if (!$guid)
            return false;

        return get_data_row("SELECT * from entities where guid=?", array($guid));
    }

    /**
     * Create an Elgg* object from a given entity row.
     */
    function entity_row_to_elggstar($row)
    {
        if (!($row instanceof stdClass))
            return $row;

        if ((!isset($row->guid)) || (!isset($row->subtype)))
            return $row;

        $classname = get_subtype_class($row->type, $row->subtype);

        if ($classname && class_exists($classname))
        {
            return new $classname($row);
        }
        else
        {
            throw new ClassException(sprintf(elgg_echo('ClassException:ClassnameNotClass'), $classname, 'ElggEntity'));
        }
    }

    /**
     * Return the entity for a given guid as the correct object.
     * @param int $guid The GUID of the entity
     * @return a child of ElggEntity appropriate for the type.
     */
    function get_entity($guid)
    {
        $entity = retrieve_cached_entity($guid);
        if (!$entity)
        {
            $entity = entity_row_to_elggstar(get_entity_as_row($guid));

            if ($entity)
            {
                cache_entity($entity);
            }
        }

        if ($entity && !has_access_to_entity($entity))
        {
            return null;
        }

        return $entity;
    }

    function get_entity_conditions(&$where, &$args, $params, $tableName='')
    {
        if ($tableName)
            $tableName .= ".";

        $subtype = $params['subtype'];
        $type = $params['type'];

        if (is_array($subtype))
        {
            $tempwhere = "";

            foreach($subtype as $typekey => $subtypearray)
            {
                foreach($subtypearray as $subtypeval)
                {
                    if (!empty($subtypeval))
                    {
                        if (!$subtypeval = (int) get_subtype_id($typekey, $subtypeval))
                            return false;
                    }
                    else
                    {
                        // @todo: Setting subtype to 0 when $subtype = '' returns entities with
                        // no subtype.  This is different to the non-array behavior
                        // but may be required in some cases.
                        $subtypeval = 0;
                    }

                    if (!empty($tempwhere))
                        $tempwhere .= " or ";

                    $tempwhere .= "({$tableName}type = ? and {$tableName}subtype = ?)";
                    $args[] = $typekey;
                    $args[] = $subtypeval;
                }
            }
            if (!empty($tempwhere))
                $where[] = "({$tempwhere})";

        }
        else
        {
            if ($type != "")
            {
                $where[] = "{$tableName}type=?";
                $args[] = $type;
            }

            $subtypeId = get_subtype_id($type, $subtype);
            if ($subtypeId)
            {
                $where[] = "{$tableName}subtype=?";
                $args[] = $subtypeId;
            }
        }

        $owner_guid = $params['owner_guid'];
        if ($owner_guid)
        {
            $where[] = "{$tableName}owner_guid = ?";
            $args[] = (int)$owner_guid;
        }

        $container_guid = $params['container_guid'];
        if ($container_guid)
        {
            $where[] = "{$tableName}container_guid = ?";
            $args[] = (int)$container_guid;
        }

        $timelower = $params['time_lower'];
        if ($timelower)
        {
            $where[] = "{$tableName}time_created >= ?";
            $args[] = (int)$timelower;
        }

        $timeupper = $params['time_upper'];
        if ($timeupper)
        {
            $where[] = "{$tableName}time_created <= ?";
            $args[] = (int)$timeupper;
        }
    }

    /**
     * Return entities matching a given query, or the number thereof
     *
     * @param string $type The type of entity (eg "user", "object" etc)
     * @param string|array $subtype The arbitrary subtype of the entity or array(type1 => array('subtype1', ...'subtypeN'), ...)
     * @param int $owner_guid The GUID of the owning user
     * @param string $order_by The field to order by; by default, time_created desc
     * @param int $limit The number of entities to return; 10 by default
     * @param int $offset The indexing offset, 0 by default
     * @param boolean $count Set to true to get a count rather than the entities themselves (limits and offsets don't apply in this context). Defaults to false.
     * @param int $site_guid The site to get entities for. Leave as 0 (default) for the current site; -1 for all sites.
     * @param int|array $container_guid The container or containers to get entities from (default: all containers).
     * @param int $timelower The earliest time the entity can have been created. Default: all
     * @param int $timeupper The latest time the entity can have been created. Default: all
     * @return array A list of entities.
     */
    function get_entities($type = "", $subtype = "", $owner_guid = 0, $order_by = "", $limit = 10, $offset = 0, $count = false, $site_guid = 0, $container_guid = null, $timelower = 0, $timeupper = 0)
    {
        global $CONFIG;

        if ($subtype === false || $subtype === null || $subtype === 0)
            return false;

        $where = array();
        $args = array();

        get_entity_conditions($where, $args, array(
            'type' => $type,
            'subtype' => $subtype,
            'owner_guid' => $owner_guid,
            'container_guid' => $container_guid,
            'time_lower' => $time_lower,
            'time_upper' => $time_upper));

        if (!$count)
        {
            $query = "SELECT * from entities where ";
        }
        else
        {
            $query = "SELECT count(guid) as total from entities where ";
        }

        foreach ($where as $w)
            $query .= " $w and ";

        $query .= get_access_sql_suffix();

        if (!$count)
        {
            if ($order_by == "")
            {
                $order_by = "time_created desc";
            }
            $order_by = sanitize_order_by($order_by);
            $query .= " order by $order_by";

            if ($limit)
            {
                $query .= " limit ".((int)$offset).", ".((int)$limit);
            }
            return array_map('entity_row_to_elggstar', get_data($query, $args));
        }
        else
        {
            $total = get_data_row($query, $args);
            return $total->total;
        }
    }

    /**
     * Returns a viewable list of entities
     *
     * @see elgg_view_entity_list
     *
     * @param string $type The type of entity (eg "user", "object" etc)
     * @param string $subtype The arbitrary subtype of the entity
     * @param int $owner_guid The GUID of the owning user
     * @param int $limit The number of entities to display per page (default: 10)
     * @param true|false $fullview Whether or not to display the full view (default: true)
     * @param true|false $viewtypetoggle Whether or not to allow gallery view
     * @param true|false $pagination Display pagination? Default: true
     * @return string A viewable list of entities
     */
    function list_entities($type= "", $subtype = "", $owner_guid = 0, $limit = 10, $fullview = true, $viewtypetoggle = false, $pagination = true) {

        $offset = (int) get_input('offset');
        $count = get_entities($type, $subtype, $owner_guid, "", $limit, $offset, true);
        $entities = get_entities($type, $subtype, $owner_guid, "", $limit, $offset);

        return elgg_view_entity_list($entities, $count, $offset, $limit, $fullview, $viewtypetoggle, $pagination);

    }

    /**
     * Gets a private setting for an entity.
     *
     * @param int $entity_guid The entity GUID
     * @param string $name The name of the setting
     * @return mixed The setting value, or false on failure
     */
    function get_private_setting($entity_guid, $name) {

        global $CONFIG;

        if ($setting = get_data_row("SELECT value from private_settings where name = ? and entity_guid = ?",
            array($name, (int)$entity_guid)
        )) {
            return $setting->value;
        }
        return false;

    }

    /**
     * Return an array of all private settings for a given
     *
     * @param int $entity_guid The entity GUID
     */
    function get_all_private_settings($entity_guid) {
        global $CONFIG;

        $entity_guid = (int) $entity_guid;

        $result = get_data("SELECT * from private_settings where entity_guid = ?", array($entity_guid));
        if ($result)
        {
            $return = array();
            foreach ($result as $r)
                $return[$r->name] = $r->value;

            return $return;
        }

        return false;
    }

    /**
     * Sets a private setting for an entity.
     *
     * @param int $entity_guid The entity GUID
     * @param string $name The name of the setting
     * @param string $value The value of the setting
     * @return mixed The setting ID, or false on failure
     */
    function set_private_setting($entity_guid, $name, $value) {

        global $CONFIG;

        $result = insert_data("INSERT into private_settings (entity_guid, name, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = ?",
            array((int)$entity_guid, $name, $value, $value)
        );
        if ($result === 0) return true;
        return $result;

    }

    /**
     * Deletes a private setting for an entity.
     *
     * @param int $entity_guid The Entity GUID
     * @param string $name The name of the setting
     * @return true|false depending on success
     *
     */
    function remove_private_setting($entity_guid, $name)
    {
        global $CONFIG;
        return delete_data("DELETE from private_settings where name = ? and entity_guid = ?",
            array($name, (int)$entity_guid));
    }

    function get_entities_by_condition($subTable, $where, $args, $order_by, $limit, $offset = 0, $count = false, $join = '')
    {
        $fromWhere = "FROM entities e INNER JOIN $subTable u ON u.guid = e.guid $join WHERE ";

        if (!$count)
        {
            $query = "SELECT e.*, u.* $fromWhere";
        }
        else
        {
            $query = "SELECT count(e.guid) as total $fromWhere";
        }

        foreach ($where as $w)
        {
            $query .= " $w and ";
        }
        $query .= get_access_sql_suffix('e');

        if (!$count)
        {
            if ($order_by)
            {
                $query .= " order by ".sanitize_order_by($order_by);
            }

            if ($limit)
            {
                $query .= " limit ".((int)$offset).", ".((int)$limit);
            }

            //echo $query;

            return array_map('entity_row_to_elggstar', get_data($query, $args));
        }
        else
        {
            $total = get_data_row($query, $args);
            return $total->total;
        }
    }
