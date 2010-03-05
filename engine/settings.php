<?php
    error_reporting(E_ERROR | E_PARSE);

    global $CONFIG;
    if (!isset($CONFIG))
        $CONFIG = new stdClass;

    $CONFIG->dbuser = 'newslink';
    $CONFIG->dbpass = 'scarlett';
    $CONFIG->dbname = 'elgg';
    $CONFIG->dbhost = 'localhost';

	/*

    // Yes! We want to split reads and writes
    $CONFIG->db->split = true;
	 
    // READS
    $CONFIG->db['read']->dbuser = "";
    $CONFIG->db['read']->dbpass = "";
    $CONFIG->db['read']->dbname = "";
    $CONFIG->db['read']->dbhost = "localhost";

    $CONFIG->db['write']->dbuser = "";
    $CONFIG->db['write']->dbpass = "";
    $CONFIG->db['write']->dbname = "";
    $CONFIG->db['write']->dbhost = "localhost";

	 */
			
	/*
	 * For extra connections for both reads and writes, you can turn both
	 * $CONFIG->db['read'] and $CONFIG->db['write'] into an array, eg:
	 * 
	 * 	$CONFIG->db['read'][0]->dbhost = "localhost";
	 * 
	 * Note that the array keys must be numeric and consecutive, i.e., they start
	 * at 0, the next one must be at 1, etc.
	 */
	 			
	/**
	 * Memcache setup (optional)
	 * This is where you may optionally set up memcache.
	 * 
	 * Requirements: 
	 * 	1) One or more memcache servers (http://www.danga.com/memcached/)
	 *  2) PHP memcache wrapper (http://uk.php.net/manual/en/memcache.setup.php)
	 * 
	 * Note: Multiple server support is only available on server 1.2.1 or higher with PECL library > 2.0.0
	 */
	//$CONFIG->memcache = true;
	//
	//$CONFIG->memcache_servers = array (
	//	array('server1', 11211),
	//	array('server2', 11211)
	//);		
	
	// Try uncommenting the below if your notification emails are not being sent
	// $CONFIG->broken_mta = true; 
			
    $CONFIG->email_pass = "f03;aoeA";    		
    $CONFIG->google_api_key = "ABQIAAAAHy69XWEjciJIVElz0OYMsRR3-IOatrPZ1tLat998tYHgwqPnkhTKyWcq8ytRPMx3RyxFjK0O7WSCHA";
        
    $CONFIG->translations['sw'] = array('sw' => 'Kiswahili');

    $CONFIG->path = dirname(dirname(__FILE__)) . "/";   
    $CONFIG->viewpath = $CONFIG->path . "views/";   
    $CONFIG->pluginspath = $CONFIG->path . "mod/";    
    $CONFIG->dataroot = dirname($CONFIG->path). "/elgg-data/";
    $CONFIG->simplecache_enabled = 0;
    $CONFIG->viewpath_cache_enabled = 0;
    $CONFIG->wwwroot = "http://localhost/elgg/";
    $CONFIG->url = $CONFIG->wwwroot;
    $CONFIG->view = "default";
    $CONFIG->language = "en";
    $CONFIG->default_access = "1";
    $CONFIG->allow_user_default_access = "0";
    $CONFIG->debug = "1";
    $CONFIG->site_guid = $CONFIG->site_id = 1;
    $CONFIG->sitename = "Envaya";
    $CONFIG->sitedescription = "";
    $CONFIG->siteemail = "youngj@envaya.org";
    $CONFIG->enabled_plugins = array("logbrowser","profile");
        
    $CONFIG->subtypes = array(
        1 => array("object", "file", "ElggFile"),
        2 => array("object", "plugin", "ElggPlugin"),
        3 => array("object", "widget", "ElggWidget"),
        4 => array('user', 'organization', "Organization"),
        5 => array('object', 'translation', 'Translation'),
        7 => array('object', 'blog', 'NewsUpdate'),        
        8 => array('object', 'logwrapper', 'ElggObject'),
        9 => array('object', 'admin_message', 'ElggObject'),
    );
    foreach ($CONFIG->subtypes as $val => $subtypeArr)
    {
        define('T_' . $subtypeArr[1], $val);
    }
    
    include_once(dirname(__FILE__) . "/localsettings.php");
?>