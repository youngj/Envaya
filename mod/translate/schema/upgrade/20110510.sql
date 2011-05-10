CREATE TABLE `interface_key_comments` (
      `guid` bigint(20) unsigned  NOT NULL,  
	`owner_guid` bigint(20) unsigned NOT NULL,
    `container_guid` bigint(20) unsigned NOT NULL,
	`time_created` int(11) NOT NULL,
	`time_updated` int(11) NOT NULL,
    `status` tinyint(4) not null default 1,	
	
    PRIMARY KEY  (`guid`),
	KEY `owner_guid` (`owner_guid`),
	KEY `container_guid` (`container_guid`),
	KEY `time_created` (`time_created`),
	KEY `time_updated` (`time_updated`),  
  `content` mediumtext NOT NULL default '',
  `thumbnail_url` text default null,        
  `language` varchar(4) default null,  
  `key_name` varchar(64) NOT NULL,
  `language_guid` bigint(20) unsigned NULL default 0,  
  KEY `language_guid` (`language_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;