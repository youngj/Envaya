<?php
    // Database username
    $CONFIG->dbuser = 'web';

    // Database password
    $CONFIG->dbpass = 'f03;aoeA';

    // Database name
    $CONFIG->dbname = 'envaya';

    // Database server
    // (For most configurations, you can leave this as 'localhost')
    $CONFIG->dbhost = 'localhost';

    $CONFIG->simplecache_enabled = 1;
    $CONFIG->domain = "envaya.org";
    
	$CONFIG->storage_backend = 'Storage_S3';
	$CONFIG->s3_key = 'AKIAJAJKJDBD2RSGAILQ';
    $CONFIG->s3_private = 'E9s2sGLEKqJyCG6WE4PbE/JMBOuLcZ4DJ2v1hyH4';
	$CONFIG->s3_bucket = 'envayadata';
	
	$CONFIG->recaptcha_key = '6LfAsr4SAAAAAIJsckkYkYjVHMN2OgaXtejNPzRX';
	$CONFIG->recaptcha_private = '6LfAsr4SAAAAAJtIid_-8xrD9arDeBT3BJCF6skT';
	
    $CONFIG->admin_email = "admin@envaya.org";
    $CONFIG->post_email = "post@envaya.org";
    
	$CONFIG->cache_backend = "MemcacheCache";	
	$CONFIG->memcache_servers = array('localhost');
	
    $CONFIG->analytics_enabled = 1;
    $CONFIG->error_emails_enabled = 1;
    $CONFIG->cookie_domain = ".envaya.org";
    $CONFIG->dataroot = "/var/elgg-data/";
    $CONFIG->debug = false;
    $CONFIG->ssl_enabled = true;