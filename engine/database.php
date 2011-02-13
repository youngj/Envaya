<?php

class Database
{
    const Write = 'write';
    const Read = 'read';

    static $DB_PROFILE = array();
    static $DB_DELAYED_QUERIES = array();

    static function get_row($query, $args = array())
    {
        $db = static::get_link(Database::Read);       
        $res = false;        
        if ($stmt = static::stmt_execute($db, $query, $args))
        {
            if ($row = $stmt->fetch())
            {
                $res = (object)$row;
            }
            $stmt->closeCursor();
        }
        return $res;
    }

    static function get_rows($query, $args = array())
    {
        $db = static::get_link(Database::Read);
        if ($stmt = static::stmt_execute($db, $query, $args))
        {
            $res = array();

            while ($row = $stmt->fetch())
            {
                $res[] = (object)$row;
            }

            $stmt->closeCursor();

            return $res;
        }
        return false;
    }

    static function insert_row($tableName, $values)
    {
        $columns = array();
        $args = array();

        foreach ($values as $column => $value)
        {
            $columns[] = "`$column`";
            $args[] = $value;
        }

        return static::insert("INSERT into $tableName (".implode(',', $columns).") VALUES (".implode(',',array_fill(0, sizeof($columns), '?')).")",
            $args
        );
    }

    static function update_row($tableName, $pkColumn, $pkValue, $values)
    {
        $columns = array();
        $args = array();

        foreach ($values as $column => $value)
        {
            $columns[] = "`$column` = ?";
            $args[] = $value;
        }
        $args[] = $pkValue;

        static::update("UPDATE $tableName SET ".implode(',', $columns)." WHERE $pkColumn = ?", $args);
    }

    static function save_row($tableName, $pkColumn, &$pkValue, $values)
    {
        if ($pkValue)
        {
            static::update_row($tableName, $pkColumn, $pkValue, $values);
        }
        else
        {
            $pkValue = static::insert_row($tableName, $values);
        }
    }

    static function update($query, $args = array())
    {
        $db = static::get_link(Database::Write);                
        if (static::stmt_execute($db, $query, $args))
        {
            return true;
        }
        return false;
    }
    
    /* 
     * Like update, but returns the id of the last inserted row 
     * (e.g. for autoincrement primary keys)
     */
    static function insert($query, $args = array())
    {
        $db = static::get_link(Database::Write);                
        if (static::stmt_execute($db, $query, $args))
        {
            return $db->lastInsertId();
        }
        return false;
    }    

    /* 
     * Like update, but returns the number of rows deleted
     */    
    static function delete($query, $args = array())
    {
        $db = static::get_link(Database::Write);        
        if ($stmt = static::stmt_execute($db, $query, $args))
        {
            return $stmt->rowCount();
        }
        return false;
    }

    static function sanitize_order_by($order_by)
    {
        if (preg_match('/[^\\w\\s\\,\\`\\.]/', $order_by))
        {
            throw new DatabaseException(sprintf(__('error:InvalidQueryParameter'), $order_by));
        }
        return $order_by;
    }

    /**
     * Queue a query for execution after all output has been sent to the user.
     */
    static function execute_delayed($query, $args = array()) 
    { 
        static::$DB_DELAYED_QUERIES[] = array(
            'query' => $query,
            'args' => $args,
            'link' => static::get_link(Database::Write)
        );
    }    
           
    private static function get_link($dblinktype)
    {
        static $DB_LINK;
        static $DB_CONNECT_TRIED = false;
        
        if (!isset($DB_LINK) && !$DB_CONNECT_TRIED)
        {              
            $DB_CONNECT_TRIED = true;
            
            try
            {
                $DB_LINK = static::get_pdo();
            }
            catch (PDOException $ex)
            {
                throw new DatabaseException(__("error:NoConnect"));
            }
        }
        return $DB_LINK;
    }

    static function init()
    {
        register_event_handler('shutdown', 'system', array('Database', 'delayedexecution_shutdown_hook'), 1);
        register_event_handler('shutdown', 'system', array('Database', 'profiling_shutdown_hook'), 999);
        return true;
    }           
        
    /**
     * Shutdown hook to display profiling information about db (debug mode)
     */
    static function profiling_shutdown_hook()
    {
        if (Config::get('debug') && sizeof(static::$DB_PROFILE) > 0)
        {
            error_log("***************** DB PROFILING ********************");

            $profile_count = array_count_values(static::$DB_PROFILE);

            foreach ($profile_count as $k => $v)
                error_log("$v times: '$k' ");

            error_log("***************************************************");
        }
    }

    /**
     * Execute any delayed queries.
     */
    static function delayedexecution_shutdown_hook()
    {
        foreach (static::$DB_DELAYED_QUERIES as $query_details)
        {
            try
            {
                $stmt = static::stmt_execute($query_details['link'], $query_details['query'], $query_details['args']);
            }
            catch (Exception $e)
            {
                // Suppress all errors since these can't be dealt with here
                if (Config::get('debug'))
                    error_log($e);
            }
        }
    }
               
    private static function get_pdo()
    {
        $dbhost = Config::get('dbhost');
        $dbname = Config::get('dbname');
        
        $pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", 
            Config::get('dbuser'), 
            Config::get('dbpass'), 
            array(PDO::ATTR_TIMEOUT => 2)
        );
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $pdo->setAttribute(PDO::ATTR_TIMEOUT, 8);
        return $pdo;
    }    
    
    private static function stmt_execute($db, $query, $args)
    {
        if (!$db)
            return null;        
    
        static::$DB_PROFILE[] = $query;   

        $stmt = $db->prepare($query);

        if (!$stmt->execute($args))
        {
            throw new DatabaseException(__("DatabaseException:ExecuteFailed"));
        }
        return $stmt;
    }    
}
