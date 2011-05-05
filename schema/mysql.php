CREATE TABLE `entities` (
	`guid` bigint(20) unsigned  NOT NULL auto_increment,	
	`subtype_id` varchar(63) not null,	
    primary key (`guid`),
	KEY `subtype_id` (`subtype_id`)    
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `files` (   
    <?php require 'schema/entity_columns.php'; ?>,
  `group_name` varchar(32) NOT NULL,
  `storage` varchar(16) null,
  `filename` varchar(64) NOT NULL,
  `width` int NULL,
  `height` int NULL,
  `size` varchar(32) NULL,
  `mime` varchar(32) NULL,

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
  `value` mediumtext NOT NULL,
  `html` tinyint not null default 0,
  
  PRIMARY KEY  (`id`),
  KEY `prop` (`container_guid`,`property`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,  
  `name` text default null,
  `email` varchar(128) default null,
  `location` text default null
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `featured_sites` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>, 
  `user_guid` bigint(20) unsigned  NOT NULL,  
  `image_url` text default null,
  `active` tinyint(4) default 0
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `featured_photos` (
    <?php require 'schema/entity_columns.php'; ?>,

  `user_guid` bigint(20) unsigned NULL,  
  `image_url` text not null,
  `x_offset` int not null default 0,
  `y_offset` int not null default 0,
  `weight` float not null default 1,
  `href` varchar(127) default null,
  `caption` text default null,
  `org_name` varchar(127) default null,  
  `language` varchar(4) default null,
  `active` tinyint(4) default 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `email_templates` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>, 
  `subject` text default null,
  `from` text default null,
  `active` tinyint(4) NOT NULL default 0 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `widgets` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,
  `widget_name` varchar(127) NOT NULL,
  `publish_status` tinyint(4) NOT NULL default 1,
  `time_published` int(11) null,
  `subclass` varchar(32) NULL,
  `menu_order` int null,
  `in_menu` tinyint(4) default 1,
  `handler_arg` varchar(64) NULL,
  `title` varchar(127) NULL,
  `num_comments` int not null default 0,
  key `name_key` (`container_guid`,`widget_name`)
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

CREATE TABLE `shared_emails` (
    `id` int(11) auto_increment primary key not null,
    `email` varchar(128) default null,
    `user_guid` bigint(20) unsigned NOT NULL,
    `time_shared` int(11) default null,    
    `url` text default null,
    key (`email`),
    key (`user_guid`),
    key (`time_shared`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

        
CREATE TABLE `org_relationships` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,

    `type` tinyint(4) not null default 0,
    
    `subject_notified` tinyint(4) NOT NULL default 0,
    `invite_subject` tinyint(4) NOT NULL default 0,
    `subject_guid` bigint(20) unsigned NOT NULL,
    `subject_name` text default null,    
    `subject_email` varchar(128) default null,
    `subject_phone` varchar(128) default null,
    `subject_website` text default null,
    `subject_logo` text default null,    
      
    `approval` int default 0,        
    `order` int default 0,
    
    KEY `subject_guid` (`subject_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
    <?php require 'schema/entity_columns.php'; ?>,
  
  `subtype_id` varchar(63) not null,
  `name` text NOT NULL,
  `username` varchar(128) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `salt`     varchar(8)  NOT NULL default '',
  `email` text NOT NULL,
  `phone_number` text NULL,
  `language` varchar(6)  NOT NULL default '',
  `last_action` int(11) NOT NULL default '0',
  `email_code` varchar(24) default NULL,
  `approval` int(11) NOT NULL default '0',
  `setup_state` int(11) NOT NULL default '0',
  `country` varchar(4) NULL,
  `city` varchar(128) NULL,  
  `icons_json` text default NULL,
  `design_json` text default null,  
  `admin` tinyint(4) default '0',
  `latitude` float null,
  `longitude` float null,
  `timezone_id` varchar(64) default null,
  `region` varchar(32) default NULL,
  `last_notify_time` int default null,
  `notifications` int(11) not null default 3,
  UNIQUE KEY (`username`),
  UNIQUE KEY (`email_code`),
  KEY `subtype_id` (`subtype_id`),
  KEY `email` (`email`(50)),
  KEY `last_action` (`last_action`)
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

CREATE TABLE `geocode_cache` (
	id     int(11)     auto_increment,
	location varchar(128),
	`lat`    varchar(20),
	`long`   varchar(20),
	
	PRIMARY KEY (`id`),
    UNIQUE KEY `location` (`location`)
	
) ENGINE=MEMORY;

CREATE TABLE `sessions` (
	`session` varchar(255) NOT NULL,
 	`ts` int(11) unsigned NOT NULL default '0',
	`data` mediumblob,
	
	PRIMARY KEY `session` (`session`),
	KEY `ts` (`ts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `state` (
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `system_log` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`object_id` int(11) NOT NULL,
	`object_class` varchar(50) NOT NULL,   
	`event` varchar(50) NOT NULL,
	`user_guid` int(11) NOT NULL,
	`time_created` int(11) NOT NULL,	
	PRIMARY KEY  (`id`),
	KEY `event` (`event`),
	KEY `user_guid` (`user_guid`),
	KEY `time_created` (`time_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `feed_items` (
	`id` INT NOT NULL AUTO_INCREMENT,		
	`feed_name` varchar(128) NOT NULL,
	`action_name` varchar(32) NOT NULL,
	`subject_guid` bigint(20) NOT NULL,
	`user_guid` bigint(20) NOT NULL,
	`time_posted` int NOT NULL,
	`args` TEXT default NULL,
	PRIMARY KEY ( `id` ),
	KEY `feed_key` (`feed_name`, `time_posted`),
	KEY `user_guid` (`user_guid`),
	KEY `subject_guid` (`subject_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8; 	

CREATE TABLE `outgoing_mail` (
	`id` INT NOT NULL AUTO_INCREMENT,		
	`email_guid` bigint(20) NULL,
	`to_guid` bigint(20) NULL,
    `from_guid` bigint(20) NULL,
    `time_created` int NULL,
    `subject` text null,
    `to_address` text NULL,
    `time_queued` int NOT NULL,
	`time_sent` int NULL,
    `status` tinyint(4) default 0,
    `error_message` text null,
    `serialized_mail` mediumtext null,    
	PRIMARY KEY ( `id` ),
	KEY `email_guid` (`email_guid`),
	KEY `to_guid` (`to_guid`),
    KEY `from_guid` (`from_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8; 	

CREATE TABLE `discussion_messages` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,
    `message_id` varchar(128) default '',
    `subject` text default '',
    `from_name` text default '',
    `from_location` text default '',
    `from_email` varchar(128) default '',
    `time_posted` int(11),    
    KEY (`message_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `discussion_topics` (
    <?php require 'schema/entity_columns.php'; ?>,

    `first_message_guid` bigint(20) unsigned NOT NULL,
    `subject` text default '',
    `last_time_posted` int(11) default 0,
    `last_from_name` text default '',
    `num_messages` int(11) default 0,
    `snippet` text default ''
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `revisions` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `owner_guid` bigint(20) unsigned NOT NULL,
    `entity_guid` bigint(20) unsigned NOT NULL,
    `time_created` int not null,
    `time_updated` int not null,
    `content` mediumtext not null,
    `publish_status` tinyint(4) not null default 0,
    KEY (`entity_guid`),
    KEY (`owner_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `not_found_redirects` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `pattern` varchar(127) not null,
    `replacement` varchar(127) not null,
    `order` int(11) not null
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `external_feeds` (
    <?php require 'schema/entity_columns.php'; ?>,
    `url` text NOT NULL,
    `subtype_id` varchar(63) not null
) ENGINE = MYISAM DEFAULT CHARSET=utf8;