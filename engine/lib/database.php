<?php

	/**
	 * Elgg database
	 * Contains database connection and transfer functionality
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	$DB_PROFILE = array();
	$DB_DELAYED_QUERIES = array();    
	
    function get_db_link($dblinktype)
    {
        global $DB_LINK, $CONFIG;
        if (!isset($DB_LINK))
        {
            $DB_LINK = new mysqli($CONFIG->dbhost, $CONFIG->dbuser, $CONFIG->dbpass, $CONFIG->dbname); 
            
            if ($DB_LINK->connect_error)
            {
                throw new DatabaseException(elgg_echo("DatabaseException:NoConnect"));
            }    
        }
        return $DB_LINK;
    }        
        
	/**
	 * Shutdown hook to display profiling information about db (debug mode)
	 */
	function db_profiling_shutdown_hook()
	{
		global $CONFIG, $DB_PROFILE;
		        
		if (isset($CONFIG->debug) && $CONFIG->debug)
		{
			error_log("***************** DB PROFILING ********************");
			
			$DB_PROFILE = array_count_values($DB_PROFILE);
			
			foreach ($DB_PROFILE as $k => $v) 
				error_log("$v times: '$k' ");
			
			error_log("***************************************************");
		}
	}
	
	/**
	 * Execute any delayed queries.
	 */
	function db_delayedexecution_shutdown_hook()
	{
		global $DB_DELAYED_QUERIES, $CONFIG;
		
		foreach ($DB_DELAYED_QUERIES as $query_details) 
        {            
            $stmt = stmt_execute($query_details['l'], $query_details['q'], $query_details['a']);
			
			try 
            {
				if ( (isset($query_details['h'])) && (is_callable($query_details['h'])))
					$query_details['h']($stmt);
			} 
            catch (Exception $e) 
            { 
                // Suppress all errors since these can't be dealt with here
				if (isset($CONFIG->debug) && $CONFIG->debug) 
                    error_log($e);
			}
		}
	}
		
	/**
	 * Alias to setup_db_connections, for use in the event handler
	 *
	 * @param string $event The event type
	 * @param string $object_type The object type
	 * @param mixed $object Used for nothing in this context
	 */
    function init_db($event, $object_type, $object = null) 
    {
        register_elgg_event_handler('shutdown', 'system', 'db_delayedexecution_shutdown_hook', 1);
        register_elgg_event_handler('shutdown', 'system', 'db_profiling_shutdown_hook', 999);
        return true;
    }

    /**
     * Queue a query for execution after all output has been sent to the user.
     *
     * You can specify a handler function if you care about the result. This function will accept
     * the raw result from mysql_query();
     *  
     * @param string $query The query to execute
     * @param resource $dblink The database link to use
     * @param string $handler The handler
     */
    function execute_delayed_query($query, $args = false, $dblink, $handler = "")
    {
        global $DB_DELAYED_QUERIES;

        if (!isset($DB_DELAYED_QUERIES))
            $DB_DELAYED_QUERIES = array();

        // Construct delayed query
        $delayed_query = array();
        $delayed_query['q'] = $query;
        $delayed_query['a'] = $args;
        $delayed_query['l'] = $dblink;
        $delayed_query['h'] = $handler;

        $DB_DELAYED_QUERIES[] = $delayed_query;

        return true;
    }

    /**
     * Write wrapper for execute_delayed_query()
     *
     * @param string $query The query to execute
     * @param string $handler The handler if you care about the result.
     */
    function execute_delayed_write_query($query, $args = false, $handler = "") { return execute_delayed_query($query, $args, get_db_link('write'), $handler); }

    /**
     * Read wrapper for execute_delayed_query()
     *
     * @param string $query The query to execute
     * @param string $handler The handler if you care about the result.
     */
    function execute_delayed_read_query($query, $args = false, $handler = "") { return execute_delayed_query($query, $args, get_db_link('read'), $handler); }
		       
    function get_data_row($query, $args = false) 
    {
        $mysqli = get_db_link('read');                                      
                    
        if ($stmt = stmt_execute($mysqli, $query, $args)) 
        {
            $out = &stmt_bind_assoc($stmt);

            $res = ($stmt->fetch()) ? make_obj_from_array($out) : false;

            $stmt->close();        
        
            return $res;
        }
            
        return false;
    }
           
    function get_data($query, $args = false) 
    {    
        $mysqli = get_db_link('read');        
            
        if ($stmt = stmt_execute($mysqli, $query, $args)) 
        {
            $out = &stmt_bind_assoc($stmt);

            $res = array();
            
            while ($stmt->fetch())
            {
                $res[] = make_obj_from_array($out);
            }

            $stmt->close();        
        
            return $res;
        }
            
        return false;
    }

    function insert_data($query, $args = false) 
    {            
        $mysqli = get_db_link('write');
        
        if ($stmt = stmt_execute($mysqli, $query, $args)) 
        {
            return $mysqli->insert_id;
        }
        return false;
    }
    
    function update_data($query, $args = false) 
    {
        $mysqli = get_db_link('write');

        if ($stmt = stmt_execute($mysqli, $query, $args)) 
        {
            return true;
        }
        return false;
    }    
    
    function delete_data($query, $args = false) 
    {
        $mysqli = get_db_link('write');

        if ($stmt = stmt_execute($mysqli, $query, $args)) 
        {
            return $mysqli->affected_rows;
        }
        return false;
    }    

    function make_obj_from_array($obj)
    {
        $res = new stdClass();
        foreach ($obj as $k => $v)
        {
            $res->$k = $v;
        }   
        return $res;
    }    

    function &stmt_bind_assoc(&$stmt) 
    {
        $data = $stmt->result_metadata();

        $out = array();
        $params = array();

        $params[0] = $stmt;    
        $count = 1;

        while($field = mysqli_fetch_field($data)) 
        {
            $params[$count] = &$out[$field->name];
            $count++;
        }    
        call_user_func_array('mysqli_stmt_bind_result', $params);
        return $out;
    }

    function stmt_execute($mysqli, $sql, $params)
    {
        $stmt = $mysqli->prepare($sql);  

        if (!$stmt)
        {
            throw new DatabaseException(elgg_echo("DatabaseException:InvalidQuery"));
        }
        
        if ($params)
        {
            $bindParams = array($stmt, '');
            $len = sizeof($params);

            for ($i = 0; $i < $len; $i++)
            {
                
                $bindParams[] = &$params[$i];

                $param = $params[$i];
                if (is_float($param))
                {
                    $p = "d";    
                }
                else if (is_int($param) || is_bool($param))
                {
                    $p = "i";
                }
                else
                {
                    $p = "s";
                }

                $bindParams[1] .= $p;
            }

            call_user_func_array('mysqli_stmt_bind_param', $bindParams);
        }    

        global $DB_PROFILE;
        $DB_PROFILE[] = $sql;

        $stmt->execute();

        return $stmt;
    }    

	/**
	 * Runs a full database script from disk
	 *
	 * @uses $CONFIG
	 * @param string $scriptlocation The full path to the script
	 */
    function run_sql_script($scriptlocation) {

        if ($script = file_get_contents($scriptlocation)) {

            global $CONFIG;

            $errors = array();

            $script = preg_replace('/\-\-.*\n/', '', $script);
            $sql_statements =  preg_split('/;[\n\r]+/', $script);
            foreach($sql_statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $result = update_data($statement);
                    } catch (DatabaseException $e) {
                        $errors[] = "$statement: $e->getMessage()";
                    }
                }
            }
            if (!empty($errors)) {
                $errortxt = "";
                foreach($errors as $error)
                    $errortxt .= " {$error};";
                throw new DatabaseException(elgg_echo('DatabaseException:DBSetupIssues') . $errortxt);
            }

        } else {
            throw new DatabaseException(sprintf(elgg_echo('DatabaseException:ScriptNotFound'), $scriptlocation));
        }

    }      

    function sanitize_order_by($order_by)
    {
        if (preg_match('/[^\\w\\s\\,\\`\\.]/', $order_by))
        {
            throw new DatabaseException(sprintf(elgg_echo('DatabaseException:UnspecifiedQueryType'), $scriptlocation));
        }
        return $order_by;
    }

    register_elgg_event_handler('boot','system','init_db',0);

?>