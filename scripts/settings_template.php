<?php

return array(
    'domain' => 'localhost',
    
    'dataroot' => "/var/envaya/data",
    
    'debug' => true, 
    'debug:media' => false,
    'debug:db_profile' => false,    
    
    'modules' => array('translate','contact','discussions','network','envaya_org'),

    'db:user' => 'root',
    'db:password' => '',
    'db:name' => 'envaya',
    'db:host' => 'localhost',    
    
    'mail:admin_email' => "root@localhost",    
    
    'mail:backend' => 'Mail_Mock',
    'mail:mock_file' => dirname(__DIR__) . "/mail.out",
    
    'site_secret' => '{{site_secret}}',
);