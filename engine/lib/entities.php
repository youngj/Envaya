<?php

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
        if ($subtype_id)
        {
            return @$CONFIG->subtypes[$subtype_id][2];
        }
        else
        {
            return @$CONFIG->types[$type];
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
    
    function entity_row_to_entity($row)
    {
        if (!$row)
            return null;

        $classname = get_subtype_class($row->type, $row->subtype);

        if ($classname && class_exists($classname))
        {   
            return new $classname($row);
        }
        else
        {
            throw new ClassException(sprintf(__('error:ClassnameNotClass'), $classname, 'Entity'));
        }
    }

    /**
     * Return the entity for a given guid as the correct object.
     * @param int $guid The GUID of the entity
     * @return a child of Entity appropriate for the type.
     */
    function get_entity($guid, $show_disabled = false)
    {
        $guid = (int)$guid;
    
        if (!$guid)
        {
            return null;
        }
    
        $entity = Entity::get_from_cache($guid);
        if (!$entity)
        {
            $entity = entity_row_to_entity(get_entity_as_row($guid));

            if ($entity)
            {
                $entity->save_to_cache();
            }
        }

        if ($entity && !$show_disabled && $entity->enabled == 'no')
        {
            return null;
        }

        return $entity;
    }
