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
    $CONFIG->wwwroot = "http://envaya.org/";
    $CONFIG->url = $CONFIG->wwwroot;
    $CONFIG->admin_email = "admin@envaya.org";
    $CONFIG->post_email = "post@envaya.org";
    $CONFIG->s3_bucket = 'envayadata';
    $CONFIG->analytics_enabled = 1;
    $CONFIG->error_emails_enabled = 1;
    $CONFIG->cookie_domain = ".envaya.org";
    $CONFIG->dataroot = "/var/elgg-data/";
    $CONFIG->debug = false;