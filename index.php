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

    Route::set('page', 'page/<name>')->defaults(array(
        'controller' => 'globalpage',
        'action' => 'view',
    )); 

    Route::set('default', '(<controller>(/<action>(/<id>)))',
        array('controller' => '(pg|home|org|admin|action|tr)?')
    )->defaults(array(
            'controller' => 'home',
            'action'     => 'index',
    ));

    Route::set('sub_item', '<username>/<controller>/<id>(/<action>)',
        array('controller' => '(post|page|topic|widget)')
    )->defaults(array(
        'action'     => 'index',
    ));

    Route::set('profile', '(<username>(/<widgetname>(/<action>)))')->defaults(array(
        'controller' => 'profile',
        'action'     => 'index',
        'widgetname' => '',
    ));

    if (get_input('login') && !Session::isloggedin())
    {
        force_login();
    }

    $request = Request::instance();    
    $request->execute();
    $request->send_headers();    
    echo $request->response;
