<?php
    /*
     * Entry point for almost all web requests handled by PHP.
     *
     * Requests are routed hierarchically by Controller classes, starting at
     * Controller_Default.  See engine/controller/default.php for the top 
     * level routing of URLs.
     */
    require __DIR__."/start.php";    

    $request = Request::instance();    

    $controller = new Controller_Default($request);
    $controller->execute($request->uri);
    
    $request->send_headers();    
    echo $request->response;
    
    