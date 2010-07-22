<?php
    /**
     * Elgg admin functions.
     * Functions for adding and manipulating options on the admin panel.
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd

     * @link http://elgg.org/
     */


    /**
     * Write a persistent message to the administrator's notification window.
     *
     * Currently this writes a message to the admin store, we may want to come up with another way at some point.
     *
     * @param string $subject Subject of the message
     * @param string $message Body of the message
     */
    function send_admin_message($subject, $message)
    {
        if (($subject) && ($message))
        {
            $admin_message = new ElggObject();
            $admin_message->subtype = T_admin_message;
            $admin_message->access_id = ACCESS_PUBLIC;
            $admin_message->title = $subject;
            $admin_message->description = $message;

            return $admin_message->save();
        }

        return false;
    }

    /**
     * List all admin messages.
     *
     * @param int $limit Limit
     */
    function list_admin_messages($limit = 10)
    {
        return list_entities('object','admin_message',0,$limit);
    }

    /**
     * Remove an admin message.
     *
     * @param int $guid The
     */
    function clear_admin_message($guid)
    {
        return delete_entity($guid);
    }

    // Register a plugin hook for permissions
    register_plugin_hook('container_permissions_check','all','admin_permissions');

?>
