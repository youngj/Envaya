<?php

class IOException extends Exception {}
class SecurityException extends Exception {}
class DatabaseException extends Exception {}
class CallException extends Exception {}
class DataFormatException extends Exception {}
class NotImplementedException extends CallException {}
class InvalidParameterException extends CallException {}

class RequestAbortedException extends Exception {}
class NotFoundException extends RequestAbortedException {}
class RedirectException extends RequestAbortedException 
{
    public $url; /* null url indicates redirect to http referrer, if possible */
    public $status;
    function __construct($msg, $url = null, $status = 302)
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
           
    $error = date("Y-m-d H:i:s (T)") . ": \"" . $errmsg . "\" in file " . $filename . " (line " . $linenum . ")";                      

    switch ($errno) {
        case E_USER_ERROR:
                error_log("ERROR: " . $error);
                SessionMessages::add_error("ERROR: " . $error);

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
    
    for ($i = ob_get_level(); $i > 0; $i--)
    {            
        ob_end_clean(); // discard all output buffers
    }
    
    if (@$_SERVER['REQUEST_URI'])
    {    
        header("HTTP/1.1 500 Internal Server Error");
        
        $request = Request::instance();
        
        if (@$request->headers['Content-Type'] == 'text/javascript')
        {
            echo json_encode(array(
                'error' => $exception->getMessage(), 
                'errorClass' => get_class($exception)
            ));
        }
        else
        {   
            echo view('layouts/base', array(
                'title' => __('exception_title'),
                'css_url' => css_url('simple'),
                'base_url' => Config::get('url'),
                'layout' => 'layouts/default',
                'hide_login' => true,
                'header' => view('page_elements/content_header', array('title' => __('exception_title'))),
                'content' => view("messages/exception", array('object' => $exception))
            ));
        }
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

            OutgoingMail::create(
                "$class: {$_SERVER['REQUEST_URI']}", 
"Exception:
==========
$ex


_SERVER:
=======
$server
        ")->send_to_admin();
        }
    }
}
