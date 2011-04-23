<?php 

/* 
 * Methods for calculating various statistics
 */
class Statistics
{    
    static function get_entity_stats()
    {
        $entity_stats = array();

        $types = Database::get_rows("SELECT distinct subtype_id from entities");
        foreach ($types as $type)
        {
            $subtype_cnt = Database::get_row("SELECT count(*) as count from entities where subtype_id = ? ", array($type->subtype_id));
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