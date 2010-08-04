<?php
    /**
     * Notifications
     * This file contains classes and functions which allow plugins to register and send notifications.
     *
     * There are notification methods which are provided out of the box (see notification_init() ). Each method
     * is identified by a string, e.g. "email".
     *
     * To register an event use register_notification_handler() and pass the method name and a handler function.
     *
     * To send a notification call notify() passing it the method you wish to use combined with a number of method
     * specific addressing parameters.
     *
     * Catch NotificationException to trap errors.
     *
     * @package Elgg
     * @subpackage API

     * @author Curverider Ltd

     * @link http://elgg.org/
     */

    /** Notification handlers */
    $NOTIFICATION_HANDLERS = array();

    function get_email_fingerprint($email)
    {
        return substr(md5($email . get_site_secret() . "-email"), 0,15);
    }

    /**
     * This function registers a handler for a given notification type (eg "email")
     *
     * @param string $method The method
     * @param string $handler The handler function, in the format "handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = NULL)". This function should return false on failure, and true/a tracking message ID on success.
     * @param array $params A associated array of other parameters for this handler defining some properties eg. supported message length or rich text support.
     */
    function register_notification_handler($method, $handler, $params = NULL)
    {
        global $NOTIFICATION_HANDLERS;

        if (is_callable($handler))
        {
            $NOTIFICATION_HANDLERS[$method] = new stdClass;

            $NOTIFICATION_HANDLERS[$method]->handler = $handler;
            if ($params)
            {
                foreach ($params as $k => $v)
                    $NOTIFICATION_HANDLERS[$method]->$k = $v;
            }

            return true;
        }

        return false;
    }

    /**
     * Notify a user via their preferences.
     *
     * @param mixed $to Either a guid or an array of guid's to notify.
     * @param int $from GUID of the sender, which may be a user, site or object.
     * @param string $subject Message subject.
     * @param string $message Message body.
     * @param array $params Misc additional parameters specific to various methods.
     * @param mixed $methods_override A string, or an array of strings specifying the delivery methods to use - or leave blank
     *              for delivery using the user's chosen delivery methods.
     * @return array Compound array of each delivery user/delivery method's success or failure.
     * @throws NotificationException
     */
    function notify_user($to, $from, $subject, $message, array $params = NULL, $methods_override = "")
    {
        global $NOTIFICATION_HANDLERS, $CONFIG;

        // Sanitise
        if (!is_array($to))
            $to = array((int)$to);
        $from = (int)$from;

        // Get notification methods
        if (($methods_override) && (!is_array($methods_override)))
            $methods_override = array($methods_override);

        $result = array();

        foreach ($to as $guid)
        {
            // Results for a user are...
            $result[$guid] = array();

            if ($guid) { // Is the guid > 0?
                // Are we overriding delivery?
                $methods = $methods_override;
                if (!$methods)
                {
                    $tmp = (array)get_user_notification_settings($guid);
                    $methods = array();
                    foreach($tmp as $k => $v)
                        if ($v) $methods[] = $k; // Add method if method is turned on for user!
                }

                if ($methods)
                {
                    // Deliver
                    foreach ($methods as $method)
                    {
                        // Extract method details from list
                        $details = $NOTIFICATION_HANDLERS[$method];
                        $handler = $details->handler;

                        if ((!$NOTIFICATION_HANDLERS[$method]) || (!$handler))
                        {
                            error_log(sprintf(__('NotificationException:NoHandlerFound'), $method));
                            continue;
                        }

                        if ($CONFIG->debug)
                            error_log("Sending message to $guid using $method");

                        $result[$guid][$method] = $handler(
                            $from ? get_entity($from) : NULL,   // From entity
                            get_entity($guid),                  // To entity
                            $subject,                           // The subject
                            $message,           // Message
                            $params                             // Params
                        );

                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get the notification settings for a given user.
     *
     * @param int $user_guid The user id
     * @return stdClass
     */
    function get_user_notification_settings($user_guid = 0)
    {
        $user_guid = (int)$user_guid;

        if ($user_guid == 0) $user_guid = get_loggedin_userid();

        $all_metadata = get_metadata_for_entity($user_guid);
        if ($all_metadata)
        {
            $prefix = "notification:method:";
            $return = new stdClass;

            foreach ($all_metadata as $meta)
            {
                $name = substr($meta->name, strlen($prefix));
                $value = $meta->value;

                if (strpos($meta->name, $prefix) === 0)
                    $return->$name = $value;
            }

            return $return;
        }

        return false;
    }

    /**
     * Set a user notification pref.
     *
     * @param int $user_guid The user id.
     * @param string $method The delivery method (eg. email)
     * @param bool $value On(true) or off(false).
     * @return bool
     */
    function set_user_notification_setting($user_guid, $method, $value)
    {
        $user_guid = (int)$user_guid;
        $user = get_entity($user_guid);

        if (!$user) $user = get_loggedin_user();

        if (($user) && ($user instanceof ElggUser))
        {
            $prefix = "notification:method:$method";
            $user->$prefix = $value;
            $user->save();

            return true;
        }

        return false;
    }

    /**
     * Notification exception.
     * @author Curverider Ltd
     */
    class NotificationException extends Exception {}


    /**
     * Send a notification via email.
     *
     * @param ElggEntity $from The from user/site/object
     * @param ElggUser $to To which user?
     * @param string $subject The subject of the message.
     * @param string $message The message body
     * @param array $params Optional parameters (none taken in this instance)
     * @return bool
     */
    function email_notify_handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = NULL)
    {
        if (!$to)
            throw new NotificationException(sprintf(__('NotificationException:MissingParameter'), 'to'));

        if (!$to->email)
            throw new NotificationException(sprintf(__('NotificationException:NoEmailAddress'), $to->guid));

        $headers = array('To' => $to->getNameForEmail());

        return send_mail($to->email, $subject, $message, $headers);
    }

    function send_mail($to, $subject, $message, $headers = null, $immediate = false)
    {
        global $CONFIG;

        if (!$headers)
        {
            $headers = array();
        }

        if (!isset($headers['From']))
        {
            $headers['From'] = "\"{$CONFIG->sitename}\" <{$CONFIG->email_from}>";
        }
        if (!isset($headers['To']))
        {
            $headers['To'] = $to;
        }

        $subject = preg_replace("/(\r\n|\r|\n)/", " ", $subject); // Strip line endings

        $headers['Subject'] = mb_encode_mimeheader($subject,"UTF-8", "B");

        if (!isset($headers['Content-Type']))
        {
            $headers["Content-Type"] = "text/plain; charset=UTF-8; format=flowed";
        }
        $headers["MIME-Version"] = "1.0";
        $headers["Content-Transfer-Encoding"] = "8bit";

        $message = wordwrap(preg_replace("/(\r\n|\r)/", "\n", $message)); // Convert to unix line endings in body

        if ($immediate)
        {
            return _send_mail_now($to, $headers, $message);
        }
        else
        {
            return FunctionQueue::queue_call('_send_mail_now', array($to, $headers, $message));
        }
    }

    function _send_mail_now($to, $headers, $message)
    {
        $mailer = get_smtp_mailer();
        echo $mailer->send($to, $headers, $message);
        return true;
    }

    function send_admin_mail($subject, $message, $headers  = null, $immediate = false)
    {
        global $CONFIG;
        return send_mail($CONFIG->admin_email, $subject, $message, $headers, $immediate);
    }

    function mock_send_mail($mail, $recipients, $headers, $body)
    {
        global $CONFIG;
        $file = fopen(getenv("MOCK_MAIL_FILE"), 'a');
        fwrite($file, "========\n");
        foreach ($headers as $k => $v)
        {
            fwrite($file, "$k: $v\n");
        }
        fwrite($file, "\n");
        fwrite($file, "$body\n\n");
        fwrite($file, "--------\n");
        fclose($file);
    }

    function get_smtp_mailer()
    {
        static $mailer;

        if (!isset($mailer))
        {
            global $CONFIG;           

            if (getenv("MOCK_MAIL_FILE"))
            {
                $mailer = new Mail_mock(array(
                    'postSendCallback' => 'mock_send_mail',
                ));
            }
            else
            {
                $mailer = new Mail_smtp(array(
                    'host' => $CONFIG->smtp_host,
                    'port' => $CONFIG->smtp_port,
                    'username' => 'web@envaya.org',
                    'auth' => true,
                    'password' => $CONFIG->email_pass));
            }
        }
        return $mailer;
    }

    /**
     * Correctly initialise notifications and register the email handler.
     *
     */
    function notification_init()
    {
        mb_internal_encoding('UTF-8');

        // Register a notification handler for the default email method
        register_notification_handler("email", "email_notify_handler");
    }

    register_event_handler('init','system','notification_init',0);
