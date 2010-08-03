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
        return make_cache_key("entity", $guid);
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
            throw new ClassException(sprintf(__('ClassException:ClassnameNotClass'), $classname, 'ElggEntity'));
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
