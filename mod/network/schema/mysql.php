CREATE TABLE `org_relationships` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,

    `type` tinyint(4) not null default 0,
    
    `subject_notified` tinyint(4) NOT NULL default 0,
    `invite_subject` tinyint(4) NOT NULL default 0,
    `subject_guid`  binary(24) NULL,
    `subject_name` text default null,    
    `subject_email` varchar(128) default null,
    `subject_phone` varchar(128) default null,
    `subject_website` text default null,
    `subject_logo` text default null,    
      
    `approval` int default 0,        
    `order` int default 0,
    
    KEY `subject_guid` (`subject_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
