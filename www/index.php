<?php
    /*
     * Entry point for almost all web requests handled by PHP.
     *
     * Requests are routed hierarchically by Controller classes, starting at
     * Controller_Default.  See engine/controller/default.php for the top
     * level routing of URLs.
     */
    require __DIR__."/../start.php";

    $controller = new Controller_Default();
    
    try
    {
        $controller->execute(Request::get_uri());
    }
    catch (RequestAbortedException $ex) {}

    $response = $controller->get_response();    
    $response->send_headers();
    echo $response->content;

