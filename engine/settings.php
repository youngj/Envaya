<?php
    error_reporting(E_ERROR | E_PARSE);

    global $CONFIG;
    $CONFIG = new stdClass;

    $CONFIG->cache_version = 91;
    
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
    $CONFIG->error_emails_enabled = 0;

    $CONFIG->s3_key = 'AKIAJAJKJDBD2RSGAILQ';
    $CONFIG->s3_private = 'E9s2sGLEKqJyCG6WE4PbE/JMBOuLcZ4DJ2v1hyH4';
    $CONFIG->s3_bucket = 'envayadev';

    $CONFIG->path = dirname(__DIR__) . "/";
    
    $CONFIG->languages = array(
        'en' => 'English',
        'sw' => 'Kiswahili'
    );

    $CONFIG->dataroot = dirname($CONFIG->path). "/elgg-data/";

    $CONFIG->simplecache_enabled = 0;

    $CONFIG->smtp_host = "smtp.com";
    $CONFIG->smtp_port = 2525;

    $CONFIG->cookie_domain = null;
    $CONFIG->domain = "localhost";    
    $CONFIG->language = "en";
    $CONFIG->debug = true;
    $CONFIG->sitename = "Envaya";

    $CONFIG->types = array(
        'object' => 'Entity',
        'user' => 'User'
    );

    $CONFIG->subtypes = array(
        1 => array("object", "file", "UploadedFile"),
        3 => array("object", "widget", "Widget"),
        4 => array('user', 'organization', "Organization"),
        //5 => array('object', 'translation', 'Translation'),
        6 => array('object', 'interface_translation', 'InterfaceTranslation'),
        7 => array('object', 'blog', 'NewsUpdate'),        
        10 => array('object', 'partnership', 'Partnership'),
        // 11 was previously used for TeamMember
        12 => array('object', 'featured_site', 'FeaturedSite'),
        13 => array('object', 'email_template', 'EmailTemplate'),
        14 => array('object', 'report_definition', 'ReportDefinition'),
        15 => array('object', 'report', 'Report'),
    );
    foreach ($CONFIG->subtypes as $val => $subtypeArr)
    {
        define('T_' . $subtypeArr[1], $val);
    }

    include_once(__DIR__ . "/localsettings.php");

    $CONFIG->url = "http://{$CONFIG->domain}/";
?>