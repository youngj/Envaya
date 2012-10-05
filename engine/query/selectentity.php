<?php

/*
 * Represents a select query for an Entity subclass.
 *
 * Normally avoids returning disabled rows.
 *
 */
class Query_SelectEntity extends Query_Select
{
    private $guid = null;
    private $subtype_ids = null;

    function guid($guid)
    {
        $this->guid = $guid;
        return $this;
    }
    
    function subtype_ids($subtype_ids)
    {
        $this->subtype_ids = $subtype_ids;
        return $this;
    }
    
    protected function finalize_query()
    {        
        if (!$this->from)
        {
            if (!$this->guid)
            {
                throw new InvalidParameterException('Cannot select Entity without table or guid');
            }
            
            $prefix = substr($this->guid, 0, 2);
            $classname = PrefixRegistry::get_class($prefix);
            
            if (!$classname)
            {   
                $this->is_empty = true;
            }
            else
            {            
                $this->from = $classname::$table_name;
                $this->set_row_class($classname);
            }
        }
        
        $prefix = $this->joins ? "{$this->get_alias()}." : "";
        
        if ($this->subtype_ids !== null)
        {
            $this->where_in("{$prefix}subtype_id", $this->subtype_ids);
        }
            
        if ($this->guid !== null)
        {
            $this->where("{$prefix}guid = ?", $this->guid);
        }    
    }    
}