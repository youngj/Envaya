<?php

class IOException extends Exception {}
class SecurityException extends Exception {}

class DatabaseException extends Exception {

    public $admin_message;

    function __construct($message = null, $admin_message = null)
    {
        parent::__construct($message);

        $this->admin_message = $admin_message;
    }
}

class CallException extends Exception {}
class DataFormatException extends Exception {}
class NotImplementedException extends CallException {}
class InvalidParameterException extends CallException {}
//class ErrorException extends Exception {}

class RequestAbortedException extends Exception {}
class NotFoundException extends RequestAbortedException {}
class PermissionDeniedException extends RequestAbortedException {}
class MethodNotAllowedException extends RequestAbortedException {}
class RedirectException extends RequestAbortedException 
{
    public $url; /* null url indicates redirect to http referrer, if possible */
    public $status;
    function __construct($msg = '', $url = null, $status = 302)
    {
        $this->url = $url;
        $this->status = $status;
        parent::__construct($msg);
    }
}

class ValidationException extends Exception 
{
    protected $is_html;
    function is_html() { return $this->is_html; }

    function __construct($msg, $is_html = false)
    {
        $this->is_html = $is_html;
        parent::__construct($msg);
    }
}

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

    switch ($errno) {
        case E_USER_ERROR:
        case E_WARNING:
        case E_USER_WARNING:
                error_log("ERROR: \"$errmsg\" in file $filename (line $linenum)");
                throw new ErrorException($errmsg, 0, $errno, $filename, $linenum);
            break;
        default:
            if (Config::get('debug'))
            {
                error_log("DEBUG: \"$errmsg\" in file $filename (line $linenum)");
            }
    }

    return true;
}

function ob_discard_all()
{
    for ($i = ob_get_level(); $i > 0; $i--)
    {            
        ob_end_clean();
    }
}

function notify_exception($exception)
{
    try
    {
        error_log("*** FATAL EXCEPTION *** : " . $exception);

        if (Config::get('mail:error_emails_enabled'))
        {
            $lastErrorEmailTimeFile = Config::get('dataroot')."/last_error_time";
            $lastErrorEmailTime = (int)@file_get_contents($lastErrorEmailTimeFile);
            $curTime = timestamp();

            if ($curTime - $lastErrorEmailTime > 60)
            {
                @file_put_contents($lastErrorEmailTimeFile, "$curTime", LOCK_EX);

                $class = get_class($exception);
                $ex = print_r($exception, true);
                $server = print_r($_SERVER, true);

                // avoid using OutgoingMail class, since it has dependencies on the Database and TaskQueue,
                // and this exception may occur because one of those components is failing.
                
                $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : @$_SERVER['PHP_SELF'];
                
                $mail = Zend::mail();
                $mail->setSubject("$class: $url");
                $mail->setBodyText("
    Exception:
    ==========
    $ex


    _SERVER:
    =======
    $server
            ");
                $mail->setFrom(Config::get('mail:email_from'), Config::get('site_name'));
                $mail->addTo(Config::get('mail:admin_email'));
                $mailer = Zend::mail_transport();
                $mail->send($mailer);
            }
        }
    }
    catch (Exception $ex) 
    {
        // suppress exceptions in exception handler to avoid php errors
    }
}

/**
 * Custom exception handler.
 * This function catches any unhandled exceptions and handles them appropriately.
 *
 * @see http://www.php.net/set-exception-handler
 * @param Exception $exception The exception being handled
 */

function php_exception_handler($exception) 
{    
    try
    {
        ob_discard_all();
        echo get_class($exception) . " " . $exception->getMessage() . "\n";
        notify_exception($exception);
    }
    catch (Exception $ex) 
    {
        // suppress exceptions in exception handler to avoid php errors
    }
    die;
}
