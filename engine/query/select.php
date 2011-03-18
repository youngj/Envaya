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

    function __construct($from = null)
    {
        $this->conditions = array();
        $this->args = array();
        $this->offset = 0;
        $this->columns = "*";
        $this->joins = array();
        $this->group_by = '';
        $this->from($from);        
    }      
    
    function set_row_function($callback)
    {
        $this->row_function = $callback;
    }
    
    function join($join)
    {
        $this->joins[] = $join;
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
            return new Query_Empty();
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
    
    function _where()
    {
        return $this->conditions;
    }
    
    function _query($columns)
    {
        $conditions = $this->_where();
        $conditions[] = '(1=1)';
        $where = implode($conditions, ' AND ');        
        
        $join = implode($this->joins, ' ');
        
        return  "SELECT {$columns} FROM {$this->from} $join WHERE $where {$this->group_by}";    
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
    
    function count()
    {
        $total = Database::get_row($this->_query("COUNT(*) as total"), $this->args);
        return $total->total;
    }
    
    function filter()
    {    
        $query = $this->_query($this->columns);
    
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
        $res = $this->filter();
        if (!empty($res))
        {
            return $res[0];
        }
        return null;
    }
}