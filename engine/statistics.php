<?php 

/* 
 * Methods for calculating various statistics
 */
class Statistics
{    
    static function get_entity_stats()
    {
        $entity_stats = array();

        $types = Database::get_rows("SELECT subtype_id, count(*) as count from entities group by subtype_id");
        foreach ($types as $type)
        {
            $entity_stats[$type->subtype_id] = $type->count;
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