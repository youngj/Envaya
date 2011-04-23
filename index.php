<?php
    /*
     * Entry point for almost all web requests handled by PHP.
     * Maps requests to controller methods depending on
     * $_SERVER['PATH_INFO'] (set by URL rewriting by the web server).
     * 
     * Uses Kohana-style routes: 
     * * "controller" maps to a PHP class in engine/controllers
     * * "action" maps to a public function of that class with name prefixed by "action_"
     */

    require_once(__DIR__."/engine/start.php");    

    Route::set('default', 
        '(<controller>(/<action>(/<id>)))',
        array('controller' => '(pg|home|org|admin|tr)?')
    )->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
    ));

    Route::set('siteitem', 
        '<username>/<controller>/<id>(/<action>)',
        array('controller' => '(post|page|topic|widget)')
    )->defaults(array(
        'action'     => 'index',
    ));

    Route::set('usersite',
        '(<username>(/<widget_name>(/<action>)))'
    )->defaults(array(
        'controller' => 'usersite',
        'action'     => 'index',
        'widget_name' => '',
    ));

    if (get_input('login') && !Session::isloggedin())
    {
        force_login();
    }

    $request = Request::instance();    
    $request->execute();
    $request->send_headers();    
    echo $request->response;
