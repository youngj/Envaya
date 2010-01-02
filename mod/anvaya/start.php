<?php

function pluginname_init() {

    // Extend system CSS with our own styles
    //extend_view('css','pluginname/css');

    // Replace the default index page
    //register_plugin_hook('index','system','new_index');
}

function new_index() {

    if (!include_once(dirname(__FILE__)) . "/index.php")
    	return false;

    return true;
}

// register for the init, system event when our plugin start.php is loaded
register_elgg_event_handler('init','system','pluginname_init');


?>