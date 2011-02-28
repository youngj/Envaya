<?php

/*
 * A place to collect HTML-encoded messages (including errors) which will be 
 * displayed at the top of the current page, when it is rendered. If the current 
 * request ends with forward() (such that messages cannot be displayed 
 * during the current request), messages will be saved to the session state, 
 * and they will be displayed on the next page.
 */
class SessionMessages
{
    static $allMessages;
    
    static function init()
    {
        if (!isset(static::$allMessages))
        {
            $messages = Session::get('messages');
            if ($messages)
            {
                static::$allMessages = $messages;
                Session::set('messages', null);
            }
            else
            {
                static::$allMessages = array();
            }
        }        
    }
    
    static function add_message($message, $register = 'messages')
    {
        static::init();
        
        if (!isset(static::$allMessages[$register]))
        {
            static::$allMessages[$register] = array();
        }
        static::$allMessages[$register][] = $message;
    }
    
    static function get_all()
    {
        static::init();
        $res = static::$allMessages;
        static::$allMessages = null;
        return $res;
    }
    
    static function view_all()
    {
        return view('messages/list', array('object' => static::get_all()));
    }
    
    static function get_register($register)
    {
        static::init();
        $res = @static::$allMessages[$register];
        unset(static::$allMessages[$register]);
        return $res;        
    }
    
    static function save()
    {
        $messages = static::get_all();
        if ($messages)
        {
            Session::set('messages', $messages);
        }        
    }
}     