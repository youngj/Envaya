<?php 

/* 
 * Methods for calculating various statistics
 */
class Statistics
{    
    static function get_entity_stats($owner_guid = 0)
    {
        $entity_stats = array();

        $query = "SELECT distinct e.subtype as subtype_id from entities e";
        $args = array();
        if ($owner_guid)
        {
            $query .= " where owner_guid=?";
            $args[] = $owner_guid;
        }

        $types = Database::get_rows($query, $args);
        foreach ($types as $type)
        {
            $args = array();

            $query = "SELECT count(*) as count from entities where subtype = ? ";
            $args[] = $type->subtype_id;

            if ($owner_guid)
            {
                $query .= " and owner_guid=? ";
                $args[] = $owner_guid;
            }

            $subtype_cnt = Database::get_row($query, $args);
            $entity_stats[$type->subtype_id] = $subtype_cnt->count;
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
        return User::query()->show_disabled($show_deactivated)->count();
    }

}