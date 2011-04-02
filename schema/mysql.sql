--
-- Main Elgg database
-- 
-- @link http://elgg.org/
--
-- --------------------------------------------------------

--
-- *** The main tables ***
--

-- Define entities. 
CREATE TABLE `entities` (
	`guid` bigint(20) unsigned  NOT NULL auto_increment,	
	`subtype` int(11) NULL,
	
	`owner_guid` bigint(20) unsigned NOT NULL,
    `container_guid` bigint(20) unsigned NOT NULL,
	
	`time_created` int(11) NOT NULL,
	`time_updated` int(11) NOT NULL,

    `status` tinyint(4) not null default 1,	
	
	primary key (`guid`),
	KEY `subtype` (`subtype`),
	KEY `owner_guid` (`owner_guid`),
	KEY `container_guid` (`container_guid`),
	KEY `time_created` (`time_created`),
	KEY `time_updated` (`time_updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- *** Entity superclass details ***
-- NB: Aside from GUID, these should not have any field names in common with the entities table.
--

CREATE TABLE `files_entity` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `group_name` varchar(32) NOT NULL,
  `storage` varchar(16) null,
  `filename` varchar(64) NOT NULL,
  `width` int NULL,
  `height` int NULL,
  `size` varchar(32) NULL,
  `mime` varchar(32) NULL,

  PRIMARY KEY  (`guid`),
  KEY `group_name` (`group_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `translations` (
  `id` int auto_increment not null,
  `guid` bigint(20) unsigned  NOT NULL default 0,
  `owner_guid` bigint(20) unsigned NOT NULL,  
  `container_guid` bigint(20) unsigned NOT NULL,
  `time_updated` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `property` varchar(32) NOT NULL,
  `lang` varchar(4) NOT NULL,
  `value` text NOT NULL,
  `html` tinyint not null default 0,
  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `interface_translations` (
  `id` int auto_increment not null,
  
  `lang` varchar(4) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `approval` int NOT NULL default 0,
  `owner_guid` bigint(20) unsigned NOT NULL,  
  
  PRIMARY KEY  (`id`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `news_updates` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `content` mediumtext NOT NULL,
  `data_types` tinyint(4) not null default 0,        
  `language` varchar(4) default null,

  `num_comments` int not null default 0,
  
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `content` text NOT NULL,
  `data_types` tinyint(4) not null default 0,        
  `language` varchar(4) default null,

  `name` text default null,
  `email` varchar(128) default null,
  `location` text default null,
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `featured_sites` (
  `guid` bigint(20) unsigned  NOT NULL,
  `user_guid` bigint(20) unsigned  NOT NULL,  
  `image_url` text default null,

  `content` text NOT NULL,
  `data_types` tinyint(4) not null default 0,        
  `language` varchar(4) default null,

  `active` tinyint(4) default 0,
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `featured_photos` (
  `guid` bigint(20) unsigned  NOT NULL,
  `user_guid` bigint(20) unsigned NULL,  
  `image_url` text not null,
  `x_offset` int not null default 0,
  `y_offset` int not null default 0,
  `weight` float not null default 1,
  `href` varchar(127) default null,
  `caption` text default null,
  `org_name` varchar(127) default null,  
  `language` varchar(4) default null,
  `active` tinyint(4) default 0,
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `email_templates` (
  `guid` bigint(20) unsigned  NOT NULL, 
  `subject` text default null,
  `from` text default null,
  `active` tinyint(4) NOT NULL default 0,
  
  `content` mediumtext NOT NULL,
  `data_types` tinyint(4) not null default 0,        
  `language` varchar(4) default null,
  
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `widgets` (
  `guid` bigint(20) unsigned  NOT NULL,
    
  `widget_name` varchar(32) NOT NULL,
  `handler_class` varchar(32) NULL,
  `menu_order` int null,
  `in_menu` tinyint(4) default 1,
  `handler_arg` varchar(64) NULL,
  `title` varchar(64) NULL,
  
  `content` mediumtext NOT NULL,
  `data_types` tinyint(4) not null default 0,        
  `language` varchar(4) default null,
  
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `invited_emails` (
    `id` int(11) auto_increment primary key not null,
    `email` varchar(128) default null,
    `registered_guid` bigint(20) unsigned NOT NULL,
    `invite_code` varchar(32) default null,    
	`last_invited` int(11) default null,
	`num_invites` int(11) default null,
    unique key (`email`),
    unique key (`invite_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        
CREATE TABLE `org_relationships` (
    `guid` bigint(20) unsigned NOT NULL,
    `type` tinyint(4) not null default 0,
    
    `subject_notified` tinyint(4) NOT NULL default 0,
    `invite_subject` tinyint(4) NOT NULL default 0,
    `subject_guid` bigint(20) unsigned NOT NULL,
    `subject_name` text default null,    
    `subject_email` varchar(128) default null,
    `subject_phone` varchar(128) default null,
    `subject_website` text default null,
    `subject_logo` text default null,    
    
    `content` mediumtext default null,
    `data_types` tinyint(4) not null default 0,        
    `language` varchar(4) default null,
  
    `approval` int default 0,        
    `order` int default 0,
    PRIMARY KEY (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Extra information relating to "users"
CREATE TABLE `users_entity` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `name` text NOT NULL,
  `username` varchar(128) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `salt`     varchar(8)  NOT NULL default '',
  `email` text NOT NULL,
  `phone_number` varchar(128) NULL,
  `canonical_phone` varchar(128) NULL,
  `language` varchar(6)  NOT NULL default '',
  `code` varchar(32) NOT NULL default '',
  `banned` enum ('yes', 'no') NOT NULL default 'no',
  
  `last_action` int(11) NOT NULL default '0',
  `prev_last_action` int(11) NOT NULL default '0',
  `last_login` int(11) NOT NULL default '0',
  `prev_last_login` int(11) NOT NULL default '0',
  `email_code` varchar(24) default NULL,
  `approval` int(11) NOT NULL default '0',
  `setup_state` int(11) NOT NULL default '0',
  `country` varchar(4) NULL,
  `city` varchar(128) NULL,
  
  `custom_icon` tinyint(4) default '0',
  `custom_header` text default NULL,
  `admin` tinyint(4) default '0',
  `latitude` float null,
  `longitude` float null,
  `timezone_id` varchar(64) default null,
  `region` varchar(32) default NULL,
  `theme` varchar(32) default NULL,
  
  `notify_days` int default 14,
  `last_notify_time` int default null,
  `notifications` int(11) not null default 3,
  PRIMARY KEY  (`guid`),
  UNIQUE KEY (`username`),
  UNIQUE KEY (`email_code`),
  KEY `password` (`password`),
  KEY `email` (`email`(50)),
  KEY `code` (`code`),
  KEY `last_action` (`last_action`),
  KEY `last_login` (`last_login`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY (`name`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `org_domain_names` (
    `id` int(11) NOT NULL auto_increment,
	`guid` bigint(20) unsigned  NOT NULL,
	`domain_name` varchar(128) NOT NULL,
    PRIMARY KEY  (`id`),
	KEY (`guid`),
	UNIQUE KEY (`domain_name`)
);

CREATE TABLE `org_phone_numbers` (
    `id` int(11) NOT NULL auto_increment,
    `phone_number` varchar(32) not null,
    `last_digits` int not null default 0,
	`org_guid` bigint(20) unsigned  NOT NULL,
	`confirmed` tinyint(4) NOT NULL default 0,
    PRIMARY KEY  (`id`),
    KEY (`phone_number`),
    KEY (`last_digits`),
	KEY (`org_guid`)
);

CREATE TABLE `sms_state` (
    `id` int(11) NOT NULL auto_increment,
    `phone_number` varchar(32) not null,
	`time_updated` int NOT NULL default 0,
	`args_json` text not null,
    PRIMARY KEY  (`id`),
    UNIQUE KEY (`phone_number`)
);

CREATE TABLE `org_sectors` (
  `id` int(11) NOT NULL auto_increment,
  
  `container_guid` bigint(20) unsigned NOT NULL,
  `sector_id` int NOT NULL,
  
  PRIMARY KEY  (`id`),
  KEY `container_guid` (`container_guid`),
  KEY `sector_id` (`sector_id`)
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Extra information relating to "groups"
CREATE TABLE `groups_entity` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `name` text NOT NULL,
  `description` text NOT NULL,
   
  PRIMARY KEY  (`guid`),
  KEY `name` (`name`(50)),
  KEY `description` (`description`(50)),
  FULLTEXT KEY (`name`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- *** Annotations and tags ***
--

CREATE TABLE `metadata` (
    `id` INT NOT NULL auto_increment,
    `entity_guid` bigint(20) unsigned  NOT NULL,
    `name` varchar(64) NOT NULL ,
    `value` TEXT NOT NULL ,
    `value_type` tinyint(4) NOT NULL,
    PRIMARY KEY ( `id` ) ,
    UNIQUE KEY ( `entity_guid` , `name` ),
    KEY `name` (`name`),
    KEY `value` (`value` (50))
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;


CREATE TABLE `cache` (
    `key` varchar(255) not null,
    `value` TEXT default null,
    `expires` int not null,
    PRIMARY KEY ( `key` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

--
-- *** Misc ***
--

-- API Users
CREATE TABLE `api_users` (
	id     int(11)     auto_increment,
	
	site_guid bigint(20) unsigned,
	
	api_key   varchar(40),
	secret    varchar(40) NOT NULL,
	active    int(1) default 1,
	
	unique key (api_key),
	primary key (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- API Sessions
CREATE TABLE `users_apisessions` (
	`id` int(11) NOT NULL auto_increment,
	`user_guid` bigint(20) unsigned NOT NULL,
  	`site_guid` bigint(20) unsigned NOT NULL,
  	
  	`token` varchar(40),
  	
  	`expires` int(11) NOT NULL,
	
	PRIMARY KEY  (`id`),
	UNIQUE KEY (`user_guid`,`site_guid`),
	KEY `token` (`token`)
) ENGINE=MEMORY;

-- HMAC Cache protecting against Replay attacks
CREATE TABLE `hmac_cache` (
	`hmac` varchar(255) NOT NULL,
	`ts` int(11) NOT NULL,

	PRIMARY KEY  (`hmac`),
	KEY `ts` (`ts`)
) ENGINE=MEMORY;

-- Geocode engine cache
CREATE TABLE `geocode_cache` (
	id     int(11)     auto_increment,
	location varchar(128),
	`lat`    varchar(20),
	`long`   varchar(20),
	
	PRIMARY KEY (`id`),
    UNIQUE KEY `location` (`location`)
	
) ENGINE=MEMORY;

-- PHP Session storage
CREATE TABLE `users_sessions` (
	`session` varchar(255) NOT NULL,
 	`ts` int(11) unsigned NOT NULL default '0',
	`data` mediumblob,
	
	PRIMARY KEY `session` (`session`),
	KEY `ts` (`ts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Datalists for things like db version
CREATE TABLE `datalists` (
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Ultra-private system settings for entities
CREATE TABLE `private_settings` (
	`id` INT NOT NULL auto_increment,
	`entity_guid` INT NOT NULL ,
	`name` varchar(128) NOT NULL ,
	`value` TEXT NOT NULL ,
	PRIMARY KEY ( `id` ) ,
	UNIQUE KEY ( `entity_guid` , `name` ),
	KEY `name` (`name`),
	KEY `value` (`value` (50))
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;


-- System log
CREATE TABLE `system_log` (
	`id` int(11) NOT NULL auto_increment,
	
	`object_id` int(11) NOT NULL,

	`object_class` varchar(50) NOT NULL,
	`object_type` varchar(50) NOT NULL,
	`object_subtype` varchar(50) NOT NULL,
	
	`event` varchar(50) NOT NULL,
	`performed_by_guid` int(11) NOT NULL,

	`owner_guid` int(11) NOT NULL,
	
	`enabled` enum ('yes', 'no') NOT NULL default 'yes',

	`time_created` int(11) NOT NULL,
	
	PRIMARY KEY  (`id`),
	KEY `object_id` (`object_id`),
	KEY `object_class` (`object_class`),
	KEY `object_type` (`object_type`),
	KEY `object_subtype` (`object_subtype`),
	KEY `event` (`event`),
	KEY `performed_by_guid` (`performed_by_guid`),
	KEY `time_created` (`time_created`),
	KEY `river_key` (`object_type`, `object_subtype`, `event`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `feed_items` (
	`id` INT NOT NULL AUTO_INCREMENT ,		
	`feed_name` varchar(128) NOT NULL,
	`action_name` varchar(32) NOT NULL,
	`subject_guid` bigint(20) NOT NULL,
	`user_guid` bigint(20) NOT NULL,
	`time_posted` int NOT NULL,
	`args` TEXT default NULL,
	`featured` tinyint(4) default 0,
	PRIMARY KEY ( `id` ),
	KEY `feed_key` (`feed_name`, `time_posted`),
	KEY `user_guid` (`user_guid`),
	KEY `subject_guid` (`subject_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8; 	

CREATE TABLE `sent_emails` (
	`id` INT NOT NULL AUTO_INCREMENT ,		
	`email_guid` bigint(20) NOT NULL,
	`user_guid` bigint(20) NOT NULL,
	`time_sent` int NOT NULL,
	PRIMARY KEY ( `id` ),
	KEY `email_guid` (`email_guid`),
	KEY `user_guid` (`user_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8; 	

CREATE TABLE `discussion_messages` (
    `guid` bigint(20) unsigned NOT NULL,
    `list_guid` bigint(20) unsigned NOT NULL,
    `message_id` varchar(128) default '',
    `subject` text default '',
    `from_name` text default '',
    `from_email` varchar(128) default '',
    `time_posted` int(11),
    
    `content` mediumtext default '',        
    `data_types` tinyint(4) not null default 0,        
    `language` varchar(4) default null,
    
    PRIMARY KEY (`guid`),
    KEY (`message_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `discussion_topics` (
    `guid` bigint(20) unsigned NOT NULL,
    `first_message_guid` bigint(20) unsigned NOT NULL,
    `subject` text default '',
    `last_time_posted` int(11) default 0,
    `last_from_name` text default '',
    `num_messages` int(11) default 0,
    `snippet` text default '',
    PRIMARY KEY (`guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `revisions` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `owner_guid` bigint(20) unsigned NOT NULL,
    `entity_guid` bigint(20) unsigned NOT NULL,
    `time_created` int not null,
    `time_updated` int not null,
    `content` mediumtext not null,
    `status` tinyint(4) not null,
    KEY (`entity_guid`),
    KEY (`owner_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;