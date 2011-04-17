<?php

/*
 * Represents a 'select' SQL query.  
 * Allows building up a SQL prepared statement in multiple steps before executing it,
 *  e.g.:
 *
 * $query = new Query_Select('my_table');
 * $query->where('foo = ?', $foo);
 * $query->limit(10);
 * $rows = $query->filter();
 * $total_num_rows = $query->count();
 *
 * Note that count() ignores limits set by limit() and returns the total
 * number of rows otherwise matching the query (using count(*)); it is not
 * the same as sizeof($rows).
 *
 * Note also that methods such as where/join/limit etc. modify the current 
 * query object and do not return a copy.
 * 
 */
class Query_Select 
{
    protected $conditions;
    protected $args;
    protected $order_by;
    protected $limit;
    protected $offset;
    protected $columns;
    protected $from;
    protected $joins;
    protected $group_by;
    protected $row_function;
    protected $is_finalized = false;
    protected $is_empty = false;

    function __construct($from = null, $class = null)
    {
        $this->conditions = array();
        $this->args = array();
        $this->offset = 0;
        $this->columns = "*";
        $this->joins = array();
        $this->group_by = '';
        $this->from($from);  

        if ($class)
        {
            $this->set_row_class($class);
        }
    }      
    
    function set_row_class($class)
    {
        $this->set_row_function(array($class, '_new'));
        return $this;
    }
    
    function set_row_function($callback)
    {
        $this->row_function = $callback;
        return $this;
    }
    
    function join($join)
    {
        $this->joins[] = $join;
        return $this;
    }
    
    function columns($columns)
    {
        $this->columns = $columns;
        return $this;
    }
    
    function from($from)
    {
        $this->from = $from;
        return $this;
    }
    
    function where_not_in($column, $values)
    {
        if (sizeof($values) == 0)
        {
            return $this;
        }
        else
        {
            return $this->where_set($column, 'NOT IN', $values);
        }
    }    
    
    function where_in($column, $values)
    {
        if (sizeof($values) == 0)
        {
            $this->is_empty = true;
            return $this;
        }
        else
        {
            return $this->where_set($column, 'IN', $values);
        }
    }    
    
    protected function where_set($column, $condition, $values)
    {
        return $this->where("$column $condition (".implode(',', array_fill(0, sizeof($values), '?')).")", $values);
    }
    
    function args($args)
    {
        foreach ($args as $arg)
        {
            if (is_array($arg))                      
            {
                foreach ($arg as $a)
                {
                    $this->args[] = $a;
                }
            }
            else
            {
                $this->args[] = $arg;
            }        
        }
        return $this;
    }
    
    function where($condition)
    {   
        $this->conditions[] = "($condition)";
        
        $args = array();
        $numArgs = func_num_args();
        
        for ($i = 1; $i < $numArgs; $i++)
        {
            $args[] = func_get_arg($i);
        }
        $this->args($args);
    
        return $this;
    }
    
    function order_by($order_by, $sanitized = false)
    {
        if (!$sanitized)
        {
            $this->order_by = Database::sanitize_order_by($order_by);
        }
        else
        {    
            $this->order_by = $order_by;
        }
        return $this;
    }
    
    function limit($limit, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }
    
    protected function get_query($columns)
    {    
        $conditions = $this->conditions;
        
        if (sizeof($conditions) > 0)
        {
            $where = "WHERE ".implode($conditions, ' AND ');        
        }
        else
        {
            $where = '';
        }
        
        $join = implode($this->joins, ' ');
        
        return  "SELECT {$columns} FROM {$this->from} $join $where {$this->group_by}";    
    }
    
    function group_by($group_by)
    {
        if ($group_by)
        {
            $this->group_by = "GROUP BY $group_by";
        }
        else
        {
            $this->group_by = '';
        }
    }
    
    protected function finalize_query()
    {
        // override to adjust things immediately before the actual query is generated        
    }
    
    private function _finalize_query()
    {    
        if (!$this->is_finalized)
        {
            $this->finalize_query();
            $this->is_finalized = true;
        }                    
    }
    
    function count()
    {
        $this->_finalize_query();
        if ($this->is_empty)
        {
            return 0;
        }
                    
        $total = Database::get_row($this->get_query("COUNT(*) as total"), $this->args);
        return (int)$total->total;
    }
    
    function filter()
    {        
        $this->_finalize_query();
        if ($this->is_empty)
        {
            return array();
        }
    
        $query = $this->get_query($this->columns);
    
        if ($this->order_by)
        {
            $query .= " order by {$this->order_by}";
        }

        if ($this->limit)
        {
            $query .= " limit ".((int)$this->offset).", ".((int)$this->limit);
        }
    
        $res = Database::get_rows($query, $this->args);        
        
        if ($this->row_function)
        {
            return array_map($this->row_function, $res);
        }
        else
        {
            return $res;
        }
    }
    
    function get()
    {
        $this->limit = 1;
    
        $res = $this->filter();
        
        if (!empty($res))
        {
            return $res[0];
        }
        return null;
    }
}