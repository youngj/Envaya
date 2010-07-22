<?php
/**
 * Abstract controller class. Controllers should only be created using a [Request].
 *
 * Controllers methods will be automatically called in the following order by
 * the request:
 *
 *     $controller = new Controller_Foo($request);
 *     $controller->before();
 *     $controller->action_bar();
 *     $controller->after();
 *
 * The controller action should add the output it creates to
 * `$this->request->response`, typically in the form of a [View], during the
 * "action" part of execution.
 *
 * @package    Kohana
 * @category   Controller
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Controller {

    /**
     * @var  object  Request that created the controller
     */
    public $request;

    /**
     * Creates a new controller instance. Each controller must be constructed
     * with the request object that created it.
     *
     * @param   object  Request that created the controller
     * @return  void
     */
    public function __construct(Request $request)
    {
        // Assign the request to the controller
        $this->request = $request;
    }

    public function page_draw($title, $body, $preBody)
    {
        $this->request->response = page_draw($title, $body, $preBody);
    }

    public function validate_security_token()
    {
        try
        {
            validate_security_token();
        }
        catch (SecurityException $ex)
        {
            forward();
            exit;
        }
    }

    public function require_login()
    {
        if (!isloggedin())
        {
            force_login();
        }
    }

    public function require_admin()
    {
        if (!isadminloggedin())
        {
            force_login();
        }
    }

    /**
     * Automatically executed before the controller action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        // Nothing by default
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the request response, add extra output, and execute
     * other custom code.
     *
     * @return  void
     */
    public function after()
    {
        // Nothing by default
    }

} // End Controller
