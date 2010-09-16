<?php

class SMS_Action
{
    function execute() {}    

    static function parse($message)
    {
        if (preg_match('/([\w]+)\b/', $message, $match))
        {
            $command = strtolower($match[1]);
        
            switch ($command)
            {
                case 'undo':
                    return new SMS_Action_Undo();                
                case 'help':
                    return new SMS_Action_Help();
                default:
                    break;
            }
        }
        if (strlen($message) < 20)
        {
            return new SMS_Action_Invalid($message);
        }
        else
        {
            return new SMS_Action_Post($message);
        }
    }
}