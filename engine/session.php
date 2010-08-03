<?php

class Session
{
    private static $started = false;
    private static $dirty = false;
    private static $cookieName = 'envaya';

    static function get($key)
    {
        if (static::$started)
        {
            return @$_SESSION[$key];
        }
        else if (@$_COOKIE[static::$cookieName])
        {
            static::start();
            return @$_SESSION[$key];
        }
        else
        {
            return null;
        }
    }

    static function destroy()
    {
        session_destroy();
        setcookie(static::$cookieName, "", 0,"/");
    }

    static function id()
    {
        if (static::$started)
        {
            return session_id();
        }
        if (!static::$started && @$_COOKIE[static::$cookieName])
        {
            static::start();
            return session_id();
        }
        return null;

    }

    static function saveInput()
    {
        static::set('input', $_POST);
    }

    static function start()
    {
        session_set_save_handler("__elgg_session_open", "__elgg_session_close", "__elgg_session_read", "__elgg_session_write", "__elgg_session_destroy", "__elgg_session_gc");

        session_name(static::$cookieName);

        session_start();
        static::$started = true;

        register_elgg_event_handler('shutdown', 'system', 'session_write_close', 10);

        if (!isset($_SESSION['__elgg_session']))
        {
            static::set('__elgg_session', md5(microtime().rand()));
        }

        $guid = @$_SESSION['guid'];
        if ($guid)
        {
            $user = get_user($guid);
            if (!$user || $user->isBanned())
            {
                static::set($_SESSION['guid'], null);
            }
        }
    }

    static function isDirty()
    {
        return static::$dirty;
    }

    static function set($key, $value)
    {
        if (!static::$started)
        {
            static::start();
        }
        if (is_null($value))
        {
            unset($_SESSION[$key]);
        }
        else
        {
            $_SESSION[$key] = $value;
        }
        static::$dirty = true;
    }
}
