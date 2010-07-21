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
     * Register an admin page with the admin panel.
     * This function extends the view "admin/main" with the provided view. This view should provide a description
     * and either a control or a link to.
     *
     * Usage:
     *  - To add a control to the main admin panel then extend admin/main
     *  - To add a control to a new page create a page which renders a view admin/subpage (where subpage is your new page -
     *    nb. some pages already exist that you can extend), extend the main view to point to it, and add controls to your
     *    new view.
     *
     * At the moment this is essentially a wrapper around extend_view.
     *
     * @param string $new_admin_view The view associated with the control you're adding
     * @param string $view The view to extend, by default this is 'admin/main'.
     * @param int $priority Optional priority to govern the appearance in the list.
     */
    function extend_elgg_admin_page( $new_admin_view, $view = 'admin/main', $priority = 500)
    {
        return extend_view($view, $new_admin_view, $priority);
    }

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
