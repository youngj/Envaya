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
            
            $row = Database::get_row("SELECT * from entities where guid=?", array($this->guid));
            if (!$row)
            {
                $this->is_empty = true;
            }
            else
            {
                $classname = EntityRegistry::get_subtype_class($row->subtype);                
                
                if (!$classname || !class_exists($classname))
                {   
                    throw new InvalidParameterException(
                        ($classname ?: "Entity subtype {$row->subtype}")." is not defined"
                    );
                }
                
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
    
    function with_metadata($meta_name, $meta_value = null)
    {
        $metadata_sql = 'SELECT entity_guid FROM metadata m where m.name = ?';
        $metadata_args = array($meta_name);
        if ($meta_value !== null)
        {
            $metadata_sql .= ' AND m.value=?';
            $metadata_args[] = $meta_value;
        }
        $this->where("guid in ($metadata_sql)");
        $this->args($metadata_args);
        
        return $this;
    }
}