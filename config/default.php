<?php

return array(
    'domain' => "localhost",    
    'cookie_domain' => null,
    'ssl_enabled' => false,
    'allow_robots' => true,    
    
    'site_secret' => 'default_secret',    
    'session_cookie_name' => 'envaya',               
    'subtype_aliases' => null, // map of old subtype_id => class name for database migrations    
    
    'modules' => array(),
    
    'site_name' => "Envaya",
    'languages' => array('en','sw','rw'),
    'language' => "en",
    
    'debug' => false,
    'debug:media' => false,
    'debug:db_profile' => false,       
    
    'db:user' => '',
    'db:password' => '',
    'db:name' => '',
    'db:port' => 3306,
    'db:host' => 'localhost',
    
    'queue:host' => "localhost",
    'queue:port' => 22133,
       
    'cache:backend' => "Cache_Database",
    'cache:version' => 202,  // increment when all cached objects need to be invalidated (rare)
            
    'analytics:backend' => "Analytics_Null",   
    
    'captcha:backend' => 'Captcha_Securimage',       
        
    'theme:default' => 'simple',
        
    'time:mock_file' => '',    
    
    'feed:page_size' => 20,
    
    'login:failure_limit' => 6,
    'login:ip_failure_limit' => 7,
    'login:failure_interval' => 15,

    'geography:geonames_user' => '',
    'geography:geonames_password' => '',
    'geography:default_timezone' => 'Africa/Dar_es_Salaam',
    'geography:countries' => array('tz','rw'),
    
);