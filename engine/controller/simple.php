<?php

/*
 * A controller that takes the name of the action function to execute
 * from the next part of the URL path.
 */
abstract class Controller_Simple extends Controller
{
    static $routes = array(
        array(
            'regex' => '/(?P<action>\w+)\b',
        ),
    );      
}