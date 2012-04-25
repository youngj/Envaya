<?php

/*
 * Represents a select query for an Entity subclass.
 *
 * Normally avoids returning disabled rows.
 *
 */
class Query_SelectEntity extends Query_Select
{
    private $show_disabled = false;
    private $guid = null;

    function show_disabled($show_disabled = true)
    {    
        $this->show_disabled = $show_disabled;
        return $this;
    }
    
    function guid($guid)
    {
        $this->guid = $guid;
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
            
        if ($this->guid !== null)
        {
            $this->where('guid = ?', $this->guid);
        }
    
        if (!$this->show_disabled)
        {
            $this->where('status <> 0');
        }
    }    
}