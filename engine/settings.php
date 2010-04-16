<?php
    error_reporting(E_ERROR | E_PARSE);

    global $CONFIG;
    if (!isset($CONFIG))
        $CONFIG = new stdClass;

    $CONFIG->dbuser = 'newslink';
    $CONFIG->dbpass = 'scarlett';
    $CONFIG->dbname = 'elgg';
    $CONFIG->dbhost = 'localhost';

    $CONFIG->queue_host = "localhost";
    $CONFIG->queue_port = 22133;
    $CONFIG->cache_backend = "MemcacheCache";
    //$CONFIG->cache_backend = "DatabaseCache";
    $CONFIG->memcache_servers = array('localhost');
            
    $CONFIG->admin_email = "nobody@envaya.org";
    $CONFIG->post_email = "postdev@envaya.org";
    $CONFIG->email_from = "web@envaya.org";
    $CONFIG->email_pass = "f03;aoeA";    		
    $CONFIG->google_api_key = "ABQIAAAAHy69XWEjciJIVElz0OYMsRR3-IOatrPZ1tLat998tYHgwqPnkhTKyWcq8ytRPMx3RyxFjK0O7WSCHA";
    $CONFIG->analytics_enabled = 0;
    
    $CONFIG->s3_key = 'AKIAJAJKJDBD2RSGAILQ';
    $CONFIG->s3_private = 'E9s2sGLEKqJyCG6WE4PbE/JMBOuLcZ4DJ2v1hyH4';
    $CONFIG->s3_bucket = 'envaya_dev';
        
    $CONFIG->translations = array(
        'en' => array('en' => 'English'),
        'sw' => array('sw' => 'Kiswahili'),
    );

    $CONFIG->path = dirname(dirname(__FILE__)) . "/";   
    $CONFIG->viewpath = $CONFIG->path . "views/";   
    $CONFIG->pluginspath = $CONFIG->path . "mod/";    
    
    $CONFIG->dataroot = dirname($CONFIG->path). "/elgg-data/";
    
    $CONFIG->simplecache_enabled = 0;
    $CONFIG->simplecache_version = 23;
    
    $CONFIG->viewpath_cache_enabled = 0;
    $CONFIG->wwwroot = "http://localhost/";
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
        
    $CONFIG->types = array(
        'object' => 'ElggObject',
        'user' => 'ElggUser'
    );
        
    $CONFIG->subtypes = array(
        1 => array("object", "file", "ElggFile"),
        2 => array("object", "plugin", "ElggPlugin"),
        3 => array("object", "widget", "Widget"),
        4 => array('user', 'organization', "Organization"),
        5 => array('object', 'translation', 'Translation'),
        6 => array('object', 'interface_translation', 'InterfaceTranslation'),
        7 => array('object', 'blog', 'NewsUpdate'),        
        8 => array('object', 'logwrapper', 'ElggObject'),
        9 => array('object', 'admin_message', 'ElggObject'),
        10 => array('object', 'partnership', 'Partnership'),
        11 => array('object', 'team_member', 'TeamMember')
    );
    foreach ($CONFIG->subtypes as $val => $subtypeArr)
    {
        define('T_' . $subtypeArr[1], $val);
    }
    
    include_once(dirname(__FILE__) . "/localsettings.php");
?>