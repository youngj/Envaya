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
	
	`type` enum ('object', 'user', 'group', 'site') NOT NULL,
	`subtype` int(11) NULL,
	
	`owner_guid` bigint(20) unsigned NOT NULL,
    `site_guid` bigint(20) unsigned NOT NULL,
    `container_guid` bigint(20) unsigned NOT NULL,
	
	`time_created` int(11) NOT NULL,
	`time_updated` int(11) NOT NULL,

	`enabled` enum ('yes', 'no') NOT NULL default 'yes',
	
	primary key (`guid`),
	KEY `type` (`type`),
	KEY `subtype` (`subtype`),
	KEY `owner_guid` (`owner_guid`),
	KEY `site_guid` (`site_guid`),
	KEY `container_guid` (`container_guid`),
	KEY `time_created` (`time_created`),
	KEY `time_updated` (`time_updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Describe relationships between entities, can describe friendships but also site membership, depending on context
CREATE TABLE `entity_relationships` (
  `id` int(11) NOT NULL auto_increment,
  
  `guid_one` bigint(20) unsigned  NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `guid_two` bigint(20) unsigned  NOT NULL,
  
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`guid_one`,`relationship`,`guid_two`),
  KEY `relationship` (`relationship`),
  KEY `guid_two` (`guid_two`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- *** Access controls ***
--

-- Table structure for table `access_collections`
CREATE TABLE `access_collections` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `owner_guid` bigint(20) unsigned NOT NULL,
  `site_guid` bigint(20) unsigned NOT NULL default '0',

  PRIMARY KEY  (`id`),
  KEY `owner_guid` (`owner_guid`),
  KEY `site_guid` (`site_guid`)
) AUTO_INCREMENT=3  ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Access containers 
CREATE TABLE `access_collection_membership` (
  `user_guid` int(11) NOT NULL,
  `access_collection_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_guid`,`access_collection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- *** Entity superclass details ***
-- NB: Aside from GUID, these should now have any field names in common with the entities table.
--

-- Extra information relating to "objects"
CREATE TABLE `objects_entity` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `title` text NOT NULL,
  `description` text NOT NULL,

  PRIMARY KEY  (`guid`),
  FULLTEXT KEY (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `files_entity` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `group_name` varchar(32) NOT NULL,
  `filename` varchar(64) NOT NULL,
  `width` int NULL,
  `height` int NULL,
  `size` varchar(32) NULL,
  `mime` varchar(32) NULL,

  PRIMARY KEY  (`guid`),
  KEY `group_name` (`group_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `translations` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `hash` varchar(64) NOT NULL,
  `property` varchar(32) NOT NULL,
  `lang` varchar(4) NOT NULL,
  `value` text NOT NULL,
  `html` tinyint not null default 0,
  
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `interface_translations` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `lang` varchar(4) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `approval` int NOT NULL default 0,
  
  PRIMARY KEY  (`guid`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `news_updates` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `content` text NOT NULL,
  `data_types` int NOT NULL,
  `language` varchar(4) default null,
  
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `featured_sites` (
  `guid` bigint(20) unsigned  NOT NULL,
  `user_guid` bigint(20) unsigned  NOT NULL,  
  `image_url` text default null,
  `content` text NOT NULL,
  `data_types` int NOT NULL,
  `language` varchar(4) default null,
  `active` tinyint(4) default 0,
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `email_templates` (
  `guid` bigint(20) unsigned  NOT NULL, 
  `subject` text default null,
  `from` text default null,
  `content` text NOT NULL,
  `data_types` int NOT NULL,
  `active` tinyint(4) NOT NULL default 0,
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
  `content` text NOT NULL,
  `data_types` int NOT NULL,
  `language` varchar(4) default null,
  
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `partnerships` (
    `guid` bigint(20) unsigned NOT NULL,
    `partner_guid` bigint(20) unsigned NOT NULL,
    `approval` smallint not null default 0,    
    `description` text,
    `language` varchar(4) default null,
    `date_formed` text,
    PRIMARY KEY (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `team_members` (
    `guid` bigint(20) unsigned NOT NULL,
    `name` text,
    `description` text,
    `data_types` smallint not null default 0,
    `list_order` int not null default 0,
    `language` varchar(4) default null,
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
  `region` varchar(32) default NULL,
  `theme` varchar(32) default NULL,
  
  `notify_days` int default 14,
  `last_notify_time` int default null,
  
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
	`guid` bigint(20) unsigned  NOT NULL,
	`domain_name` varchar(128) NOT NULL,
	KEY (`guid`),
	UNIQUE KEY (`domain_name`)
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
    `value_type` enum ('integer','text','json') NOT NULL,
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