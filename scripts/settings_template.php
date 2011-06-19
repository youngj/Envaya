<?php

return array(
    'domain' => 'localhost',

    'dbuser' => 'root',
    'dbpass' => '',
    'dbname' => 'envaya',
    'dbhost' => 'localhost',    
    
    'admin_email' => "root@localhost",    
    
    'mock_mail_file' => dirname(__DIR__) . "/mail.out",
    
    'site_secret' => '{{site_secret}}',
);