<?php

$DB_PROFILE = array();
$DB_DELAYED_QUERIES = array();
$DB_CONNECT_TRIED = false;

function get_db_link($dblinktype)
{
    global $DB_LINK, $CONFIG;
    global $DB_CONNECT_TRIED;
    if (!isset($DB_LINK) && !$DB_CONNECT_TRIED)
    {              
        $DB_CONNECT_TRIED = true;
        
        try
        {
            $DB_LINK = new PDO("mysql:host={$CONFIG->dbhost};dbname={$CONFIG->dbname}", $CONFIG->dbuser, $CONFIG->dbpass, array(
                PDO::ATTR_TIMEOUT => 2
            ));
            $DB_LINK->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $DB_LINK->setAttribute(PDO::ATTR_TIMEOUT, 8);
        }
        catch (PDOException $ex)
        {
            throw new DatabaseException(__("error:NoConnect"));
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

    if ($CONFIG->debug && isset($DB_PROFILE))
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

    if (isset($DB_DELAYED_QUERIES))
    {
        foreach ($DB_DELAYED_QUERIES as $query_details)
        {
            try
            {
                $stmt = stmt_execute($query_details['l'], $query_details['q'], $query_details['a']);
            
                if ( (isset($query_details['h'])) && (is_callable($query_details['h'])))
                    $query_details['h']($stmt);
            }
            catch (Exception $e)
            {
                // Suppress all errors since these can't be dealt with here
                if ($CONFIG->debug)
                    error_log($e);
            }
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
    register_event_handler('shutdown', 'system', 'db_delayedexecution_shutdown_hook', 1);
    register_event_handler('shutdown', 'system', 'db_profiling_shutdown_hook', 999);
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
function execute_delayed_query($query, $args = array(), $dblink, $handler = "")
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
function execute_delayed_write_query($query, $args = array(), $handler = "") { return execute_delayed_query($query, $args, get_db_link('write'), $handler); }

function get_data_row($query, $args = array())
{
    $db = get_db_link('read');

    $res = false;

    if ($stmt = stmt_execute($db, $query, $args))
    {
        if ($row = $stmt->fetch())
        {
            $res = (object)$row;
        }
        $stmt->closeCursor();
    }

    return $res;
}

function get_data($query, $args = array())
{
    $db = get_db_link('read');

    if ($stmt = stmt_execute($db, $query, $args))
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

function insert_db_row($tableName, $values)
{
    $columns = array();
    $args = array();

    foreach ($values as $column => $value)
    {
        $columns[] = "`$column`";
        $args[] = $value;
    }

    return insert_data("INSERT into $tableName (".implode(',', $columns).") VALUES (".implode(',',array_fill(0, sizeof($columns), '?')).")",
        $args
    );
}

function update_db_row($tableName, $pkColumn, $pkValue, $values)
{
    $columns = array();
    $args = array();

    foreach ($values as $column => $value)
    {
        $columns[] = "`$column` = ?";
        $args[] = $value;
    }
    $args[] = $pkValue;

    insert_data("UPDATE $tableName SET ".implode(',', $columns)." WHERE $pkColumn = ?", $args);
}


function save_db_row($tableName, $pkColumn, &$pkValue, $values)
{
    if ($pkValue)
    {
        update_db_row($tableName, $pkColumn, $pkValue, $values);
    }
    else
    {
        $pkValue = insert_db_row($tableName, $values);
    }
}

function insert_data($query, $args = array())
{
    $db = get_db_link('write');

    if (stmt_execute($db, $query, $args))
    {
        return $db->lastInsertId();
    }
    return false;
}

function update_data($query, $args = array())
{
    $db = get_db_link('write');

    if (stmt_execute($db, $query, $args))
    {
        return true;
    }
    return false;
}

function delete_data($query, $args = array())
{
    $db = get_db_link('write');

    if ($stmt = stmt_execute($db, $query, $args))
    {
        return $stmt->rowCount();
    }
    return false;
}

function stmt_execute($db, $query, $args)
{
    global $DB_PROFILE;
    $DB_PROFILE[] = $query;   

    $stmt = $db->prepare($query);

    if (!$stmt->execute($args))
    {
        throw new DatabaseException(__("DatabaseException:ExecuteFailed"));
    }
    return $stmt;
}

function sanitize_order_by($order_by)
{
    if (preg_match('/[^\\w\\s\\,\\`\\.]/', $order_by))
    {
        throw new DatabaseException(sprintf(__('error:InvalidQueryParameter'), $order_by));
    }
    return $order_by;
}

register_event_handler('init','system','init_db',0);
