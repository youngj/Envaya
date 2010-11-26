<?php
    error_reporting(E_ERROR | E_PARSE);

    global $CONFIG;
    $CONFIG = new stdClass;

    $CONFIG->cache_version = 100;
    
    $CONFIG->dbuser = 'newslink';
    $CONFIG->dbpass = 'scarlett';
    $CONFIG->dbname = 'elgg';
    $CONFIG->dbhost = 'localhost';

    $CONFIG->queue_host = "localhost";
    $CONFIG->queue_port = 22133;
   
    $CONFIG->cache_backend = "DatabaseCache";

    $CONFIG->admin_email = "nobody@envaya.org";
    $CONFIG->post_email = "postdev@envaya.org";
    $CONFIG->email_from = "web@envaya.org";
    $CONFIG->email_pass = "f03;aoeA";
    
    $CONFIG->google_api_key = "ABQIAAAAHy69XWEjciJIVElz0OYMsRR3-IOatrPZ1tLat998tYHgwqPnkhTKyWcq8ytRPMx3RyxFjK0O7WSCHA";
    $CONFIG->analytics_enabled = 0;
    $CONFIG->error_emails_enabled = 0;
    $CONFIG->ssl_enabled = false;

	$CONFIG->storage_backend = 'Storage_Local';

    $CONFIG->path = dirname(__DIR__) . "/";
    
    $CONFIG->languages = array(
        'en' => 'English',
        'sw' => 'Kiswahili'
    );
    $CONFIG->language = "en";

    $CONFIG->dataroot = dirname($CONFIG->path). "/envayadata/";
	
    $CONFIG->simplecache_enabled = 0;

    $CONFIG->smtp_host = "smtp.com";
    $CONFIG->smtp_port = 2525;
    $CONFIG->smtp_pass = "f03;aoeA";

    $CONFIG->cookie_domain = null;
    $CONFIG->domain = "localhost";    
    $CONFIG->debug = true;
    $CONFIG->sitename = "Envaya";

    include_once(__DIR__ . "/localsettings.php");

    $CONFIG->url = "http://{$CONFIG->domain}/";
    $CONFIG->secure_url = ($CONFIG->ssl_enabled) ? "https://{$CONFIG->domain}/" : $CONFIG->url;
?>