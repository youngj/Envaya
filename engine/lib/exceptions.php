<?php

class IOException extends Exception {}
class ClassException extends Exception {}
class ConfigurationException extends Exception {}
class SecurityException extends Exception {}
class DatabaseException extends Exception {}
class APIException extends Exception {}
class CallException extends Exception {}
class DataFormatException extends Exception {}
class InvalidClassException extends ClassException {}
class ClassNotFoundException extends ClassException {}
class InstallationException extends ConfigurationException {}
class NotImplementedException extends CallException {}
class InvalidParameterException extends CallException {}
class RegistrationException extends Exception {}
class PossibleDuplicateException extends Exception 
{
    public $duplicates;

    function __construct($msg, $duplicates)
    {
        parent::__construct($msg);
        $this->duplicates = $duplicates;
    }
}
class NotificationException extends Exception {}

/**
 * Error handling
 */

/**
 * PHP Error handler function.
 * This function acts as a wrapper to catch and report PHP error messages.
 *
 * @see http://www.php.net/set-error-handler
 * @param int $errno The level of the error raised
 * @param string $errmsg The error message
 * @param string $filename The filename the error was raised in
 * @param int $linenum The line number the error was raised at
 * @param array $vars An array that points to the active symbol table at the point that the error occurred
 */
function php_error_handler($errno, $errmsg, $filename, $linenum, $vars)
{            
    if (error_reporting() == 0) // @ sign
        return true; 
           
    $error = date("Y-m-d H:i:s (T)") . ": \"" . $errmsg . "\" in file " . $filename . " (line " . $linenum . ")";                      

    switch ($errno) {
        case E_USER_ERROR:
                error_log("ERROR: " . $error);
                register_error("ERROR: " . $error);

                // Since this is a fatal error, we want to stop any further execution but do so gracefully.
                throw new Exception($error);
            break;

        case E_WARNING :
        case E_USER_WARNING :
                error_log("WARNING: " . $error);                        
            break;

        default:
            if (Config::get('debug'))
            {
                error_log("DEBUG: " . $error);
            }
    }

    return true;
}

/**
 * Custom exception handler.
 * This function catches any thrown exceptions and handles them appropriately.
 *
 * @see http://www.php.net/set-exception-handler
 * @param Exception $exception The exception being handled
 */

function php_exception_handler($exception) {
    
    error_log("*** FATAL EXCEPTION *** : " . $exception);
    ob_end_clean(); // Wipe any existing output buffer
    
    if (@$_SERVER['REQUEST_URI'])
    {    
        $body = view("messages/exceptions/exception",array('object' => $exception));        
        header("HTTP/1.1 500 Internal Server Error");
        echo page_draw(__('exception_title'), $body);
    }
    else // CLI
    {
        echo $exception;
    }

    if (Config::get('error_emails_enabled'))
    {
        $lastErrorEmailTimeFile = Config::get('dataroot')."last_error_time";
        $lastErrorEmailTime = (int)file_get_contents($lastErrorEmailTimeFile);
        $curTime = time();

        if ($curTime - $lastErrorEmailTime > 60)
        {
            file_put_contents($lastErrorEmailTimeFile, "$curTime", LOCK_EX);

            $class = get_class($exception);
            $ex = print_r($exception, true);
            $server = print_r($_SERVER, true);

            send_admin_mail("$class: {$_SERVER['REQUEST_URI']}", "
Exception:
==========
$ex



_SERVER:
=======
$server
        ", null, true);
        }
    }
}
