<?php 

class Statistics
{    
    static function get_entity_stats($owner_guid = 0)
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
        return ElggUser::query()->show_disabled($show_deactivated)->count();
    }

}