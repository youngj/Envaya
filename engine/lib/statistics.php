<?php
    /**
     * Elgg statistics library.
     * This file contains a number of functions for obtaining statistics about the running system.
     * These statistics are mainly used by the administration pages, and is also where the basic views for statistics
     * are added.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    /**
     * Return an array reporting the number of various entities in the system.
     *
     * @param int $owner_guid Optional owner of the statistics
     * @return array
     */
    function get_entity_statistics($owner_guid = 0)
    {
        $entity_stats = array();

        $query = "SELECT distinct e.type,e.subtype as subtype_id from entities e";
        $args = array();
        if ($owner_guid)
        {
            $query .= " where owner_guid=?";
            $args[] = $owner_guid;
        }

        $types = get_data($query, $args);
        foreach ($types as $type)
        {
            $subtype = get_subtype_from_id($type->subtype_id);

            $args = array();

            $query = "SELECT count(*) as count from entities where type = ? ";
            $args[] = $type->type;

            if ($owner_guid)
            {
                $query .= " and owner_guid=? ";
                $args[] = $owner_guid;
            }

            if ($subtype)
            {
                $query .= " and subtype = ?";
                $args[] = $type->subtype_id;
            }

            $subtype_cnt = get_data_row($query, $args);

            if (!is_array($entity_stats[$type->type]))
                $entity_stats[$type->type] = array();

            if ($subtype)
                $entity_stats[$type->type][$subtype] = $subtype_cnt->count;
            else
                $entity_stats[$type->type]['__base__'] = $subtype_cnt->count;
        }

        return $entity_stats;
    }

    /**
     * Return the number of users registered in the system.
     *
     * @param bool $show_deactivated
     * @return int
     */
    function get_number_users($show_deactivated = false)
    {
        global $CONFIG;

        $access = "";

        if (!$show_deactivated)
            $access = "and " . get_access_sql_suffix();

        $result = get_data_row("SELECT count(*) as count from entities where type='user' $access");

        if ($result)
            return $result->count;

        return false;
    }

    /**
     * Return a list of how many users are currently online, rendered as a view.
     */
    function get_online_users()
    {
        $offset = get_input('offset',0);
        $count = count(find_active_users(600,9999));
        $objects = find_active_users(600,10,$offset);

        if ($objects)
        {
            return elgg_view_entity_list($objects, $count,$offset,10,false);
        }
    }
