<?php
    /**
     * Elgg system log.
     * Listens to events and writes crud events into the system log database.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    /**
     * Interface that provides an interface which must be implemented by all objects wishing to be
     * recorded in the system log (and by extension the river).
     *
     * This interface defines a set of methods that permit the system log functions to hook in and retrieve
     * the necessary information and to identify what events can actually be logged.
     *
     * To have events involving your object to be logged simply implement this interface.
     *
     * @author Curverider Ltd
     */
    interface Loggable
    {
        /**
         * Return an identification for the object for storage in the system log.
         * This id must be an integer.
         *
         * @return int
         */
        public function getSystemLogID();

        /**
         * Return the class name of the object.
         * Added as a function because get_class causes errors for some reason.
         */
        public function getClassName();

        /**
         * Return the type of the object - eg. object, group, user, relationship, metadata, annotation etc
         */
        public function getType();

        /**
         * Return a subtype. For metadata & annotations this is the 'name' and for relationship this is the relationship type.
         */
        public function getSubtype();

        /**
         * For a given ID, return the object associated with it.
         * This is used by the river functionality primarily.
         * This is useful for checking access permissions etc on objects.
         */
        public function getObjectFromID($id);
    }

    function system_log_query()
    {
        $query = new Query_Select('system_log');
        $query->order_by('time_created desc');
        return $query;
    }

    /**
     * Return a specific log entry.
     *
     * @param int $entry_id The log entry
     */
    function get_log_entry($entry_id)
    {
        global $CONFIG;

        $entry_id = (int)$entry_id;

        return get_data_row("SELECT * from system_log where id=?", array($entry_id));
    }

    /**
     * Return the object referred to by a given log entry
     *
     * @param int $entry_id The log entry
     */
    function get_object_from_log_entry($entry_id)
    {
        $entry = get_log_entry($entry_id);

        if ($entry)
        {
            $class = $entry->object_class;
            $tmp = new $class();
            $object = $tmp->getObjectFromID($entry->object_id);

            if ($object)
                return $object;
        }

        return false;
    }

    /**
     * Log a system event related to a specific object.
     *
     * This is called by the event system and should not be called directly.
     *
     * @param $object The object you're talking about.
     * @param $event String The event being logged
     */
    function system_log($object, $event)
    {
        global $CONFIG;
        static $logcache;

        if ($object instanceof Loggable)
        {
            if (!is_array($logcache))
                $logcache = array();

            // Has loggable interface, extract the necessary information and store
            $object_id = (int)$object->getSystemLogID();
            $object_class = $object->getClassName();
            $object_type = $object->getType();
            $object_subtype = $object->getSubtype();
            $time = time();
            $performed_by = (int)@$_SESSION['guid'];

            if (isset($object->enabled))
                $enabled = $object->enabled;
            else
                $enabled = 'yes';

            if (isset($object->owner_guid))
                $owner_guid = $object->owner_guid;
            else
                $owner_guid = 0;

            if (!isset($logcache[$time][$object_id][$event]))
            {
                insert_data("INSERT DELAYED into system_log (
                    object_id, object_class, object_type, object_subtype, event,
                    performed_by_guid, owner_guid,  enabled, time_created)
                    VALUES (?,?,?,?,?,?,?,?,?)",
                    array($object_id,$object_class,$object_type,$object_subtype,$event,
                        $performed_by,$owner_guid,$enabled,$time)
                );

                $logcache[$time][$object_id][$event] = true;
            }

            return true;

        }
    }

    /**
     * This function creates an archive copy of the system log.
     *
     * @param int $offset An offset in seconds from now to archive (useful for log rotation)
     */
    function archive_log($offset = 0)
    {
        global $CONFIG;

        $offset = (int)$offset;
        $now = time(); // Take a snapshot of now

        $ts = $now - $offset;

        // create table
        if (!update_data("CREATE TABLE system_log_$now as SELECT * from system_log WHERE time_created<?", array($ts)))
            return false;

        // delete
        if (delete_data("DELETE from system_log WHERE time_created<?", array($ts))===false)
            return false;

        // alter table to engine
        if (!update_data("ALTER TABLE system_log_$now engine=archive"))
            return false;

        return true;
    }

    /**
     * System log listener.
     * This  listens to all events in the system and logs anything appropriate.
     *
     * @param String $event
     * @param String $object_type
     * @param Loggable $object
     */
    function system_log_listener($event, $object_type, $object)
    {
        system_log($object, $event);
        return true;
    }

    register_event_handler('all','all','system_log_listener', 400);    
