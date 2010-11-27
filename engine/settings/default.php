<?php
    error_reporting(E_ERROR | E_PARSE);

    global $CONFIG;
    $CONFIG = new stdClass;
    
    $CONFIG->cache_version = 100;
    
    $CONFIG->dbuser = '';
    $CONFIG->dbpass = '';
    $CONFIG->dbname = '';    
    $CONFIG->dbhost = 'localhost';

    $CONFIG->queue_host = "localhost";
    $CONFIG->queue_port = 22133;
   
    $CONFIG->cache_backend = "DatabaseCache";

    $CONFIG->admin_email = "admin@localhost";
    $CONFIG->post_email = "post@localhost";
    $CONFIG->email_from = "web@localhost";
    $CONFIG->email_pass = "";
    
    $CONFIG->google_api_key = "ABQIAAAAHy69XWEjciJIVElz0OYMsRR3-IOatrPZ1tLat998tYHgwqPnkhTKyWcq8ytRPMx3RyxFjK0O7WSCHA";
    
    $CONFIG->analytics_enabled = 0;
    $CONFIG->error_emails_enabled = 0;
    $CONFIG->ssl_enabled = false;

	$CONFIG->storage_backend = 'Storage_Local';

    $CONFIG->path = dirname(dirname(__DIR__)) . "/";
    
    $CONFIG->languages = array(
        'en' => 'English',
        'sw' => 'Kiswahili'
    );
    $CONFIG->language = "en";

    $CONFIG->dataroot = dirname($CONFIG->path). "/envayadata/";
	
    $CONFIG->simplecache_enabled = 0;

    $CONFIG->smtp_host = "localhost";
    $CONFIG->smtp_port = 25;
    $CONFIG->smtp_pass = "";

    $CONFIG->cookie_domain = null;
    $CONFIG->domain = "localhost";    
    $CONFIG->debug = true;
    $CONFIG->sitename = "Envaya";

    $CONFIG->recaptcha_key = '';
	$CONFIG->recaptcha_private = '';    
?>