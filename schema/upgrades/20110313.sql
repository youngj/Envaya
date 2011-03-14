ALTER TABLE entities DROP `type`;
ALTER TABLE entities DROP `site_guid`;

CREATE TABLE `org_relationships` (
    `guid` bigint(20) unsigned NOT NULL,
    `type` tinyint(4) not null default 0,
    
    `subject_guid` bigint(20) unsigned NOT NULL,
    `subject_name` text default null,    
    `subject_email` varchar(128) default null,
    `subject_website` text default null,
    `subject_logo` text default null,
    
    `invite_code` varchar(32) default null,    
    
    `content` text default null,
    `language` varchar(4) default null,
    `approval` int default 0,    
    
    `order` int default 0,
    PRIMARY KEY (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
