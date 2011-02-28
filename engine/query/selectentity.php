<?php

/*
 * Represents a select query for an Entity subclass.
 *
 * Includes an implicit join on the entities table and 
 * normally avoids returning disabled rows.
 *
 * For where() statements, the entities table is named e 
 * and the subclass's table is named u.
 */
class Query_SelectEntity extends Query_Select
{
    private $show_disabled;

    function __construct($sub_table)
    {
        parent::__construct("entities e");
        $this->set_row_function('entity_row_to_entity');
        $this->join("INNER JOIN $sub_table u ON u.guid = e.guid");        
        $this->show_disabled = false;
    }   
    
    function show_disabled($show_disabled = true)
    {
        $this->show_disabled = $show_disabled;
        return $this;
    }
    
    function _where()
    {
        $conditions = $this->conditions;
        if (!$this->show_disabled)
        {
            $conditions[] = "enabled = 'yes'";
        }   
        return $conditions;        
    }
}