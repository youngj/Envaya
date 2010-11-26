<?php

    // todo: this is gross, make it go away... 
    global $ENTITY_TYPES, $ENTITY_SUBTYPES;
   
    $ENTITY_TYPES = array(
        'object' => 'Entity',
        'user' => 'User'
    );

    $ENTITY_SUBTYPES = array(
        1 => array("object", "file", "UploadedFile"),
        3 => array("object", "widget", "Widget"),
        4 => array('user', 'organization', "Organization"),
        6 => array('object', 'interface_translation', 'InterfaceTranslation'),
        7 => array('object', 'blog', 'NewsUpdate'),        
        10 => array('object', 'partnership', 'Partnership'),
        12 => array('object', 'featured_site', 'FeaturedSite'),
        13 => array('object', 'email_template', 'EmailTemplate'),
        14 => array('object', 'report_definition', 'ReportDefinition'),
        15 => array('object', 'report', 'Report'),
        16 => array('object', 'comment', 'Comment'),
    );
    foreach ($ENTITY_SUBTYPES as $val => $subtypeArr)
    {
        define('T_' . $subtypeArr[1], $val);
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
        global $ENTITY_SUBTYPES;
        if (isset($ENTITY_SUBTYPES[$subtype_id]))
        {
            return $ENTITY_SUBTYPES[$subtype_id][1];
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
        if ($subtype_id)
        {
            global $ENTITY_SUBTYPES;            
            return @$ENTITY_SUBTYPES[$subtype_id][2];
        }
        else
        {
            global $ENTITY_TYPES;
            return @$ENTITY_TYPES[$type];
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
