CREATE TABLE `files` (   
    <?php require 'schema/entity_columns.php'; ?>,
  `group_name` varchar(32) NOT NULL,
  `storage` varchar(16) null,
  `filename` text NOT NULL,
  `width` int NULL,
  `height` int NULL,
  `size` varchar(32) NULL,
  `mime` varchar(32) NULL,

  KEY `group_name` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,  
  `name` text default null,
  `email` varchar(128) default null,
  `location` text default null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `widgets` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,
  `widget_name` varchar(127) NOT NULL,
  `user_guid` binary(24) not null,  
  `local_id` int(11) unsigned null,
  `publish_status` tinyint(4) NOT NULL default 1,
  `time_published` bigint(20) unsigned null,
  `subtype_id` varchar(63) NULL,
  `menu_order` int null,
  `in_menu` tinyint(4) default 1,
  `handler_arg` varchar(64) NULL,
  `title` varchar(127) NULL,
  `num_comments` int not null default 0,
  `feed_guid` binary(24) NULL,
  key `name_key` (`container_guid`,`widget_name`),
  KEY `local_id_key` (`user_guid`,`local_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `invited_emails` (
    `id` int(11) auto_increment primary key not null,
    `email` varchar(128) default null,
    `registered_guid` binary(24) NULL,
    `invite_code` varchar(32) default null,    
	`last_invited` bigint(20) unsigned default null,
	`num_invites` int(11) default null,
    unique key (`email`),
    unique key (`invite_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `shared_emails` (
    `id` int(11) auto_increment primary key not null,
    `email` varchar(128) default null,
    `user_guid` binary(24) NOT NULL,
    `time_shared` bigint(20) unsigned default null,    
    `url` text default null,
    key (`email`),
    key (`user_guid`),
    key (`time_shared`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
CREATE TABLE `users` (
    <?php require 'schema/entity_columns.php'; ?>,
  
  `subtype_id` varchar(63) not null,
  `name` text NOT NULL,
  `username` varchar(128) NOT NULL default '',  
  `password` varchar(128) NOT NULL default '',
  `password_time` bigint(20) unsigned NULL,
  `salt`     varchar(8)  NOT NULL default '',
  `email` text NOT NULL,
  `phone_number` text NULL,
  `language` varchar(6)  NOT NULL default '',
  `last_action` bigint(20) unsigned NOT NULL default '0',
  `email_code` varchar(24) default NULL,
  `approval` int(11) NOT NULL default '0',
  `setup_state` int(11) NOT NULL default '0',
  `country` varchar(4) NULL,
  `city` varchar(128) NULL,  
  `icons_json` text default NULL,
  `design_json` text default null,  
  `latitude` float null,
  `longitude` float null,
  `timezone_id` varchar(64) default null,
  `region` varchar(32) default NULL,
  UNIQUE KEY (`username`),
  UNIQUE KEY (`email_code`),
  KEY `subtype_id` (`subtype_id`),
  KEY `email` (`email`(50)),
  KEY `last_action` (`last_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `org_domain_names` (
    `id` int(11) NOT NULL auto_increment,
	`guid` binary(24)  NOT NULL,
	`domain_name` varchar(128) NOT NULL,
    PRIMARY KEY  (`id`),
	KEY (`guid`),
	UNIQUE KEY (`domain_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_phone_numbers` (
    `id` int(11) NOT NULL auto_increment,
    `phone_number` varchar(32) not null,
    `last_digits` int not null default 0,
	`user_guid` binary(24) NOT NULL,
	`confirmed` tinyint(4) NOT NULL default 0,
    PRIMARY KEY  (`id`),
    KEY (`phone_number`),
    KEY (`last_digits`),
	KEY (`user_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sms_state` (
    `id` int(11) NOT NULL auto_increment,
    `service_id` varchar(32) not null,
    `phone_number` varchar(32) not null,
    `user_guid` binary(24) default 0,
	`time_updated` int NOT NULL default 0,
	`value` text not null,
    PRIMARY KEY  (`id`),
    KEY (`user_guid`),
    UNIQUE KEY (`phone_number`,`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `org_sectors` (
  `id` int(11) NOT NULL auto_increment,
  
  `container_guid` binary(24) NOT NULL,
  `sector_id` int NOT NULL,
  
  PRIMARY KEY  (`id`),
  KEY `container_guid` (`container_guid`),
  KEY `sector_id` (`sector_id`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cache` (
    `key` varbinary(255) not null,
    `value` TEXT default null,
    `expires` int not null,
    PRIMARY KEY ( `key` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `geocode_cache` (
	id     int(11)     auto_increment,
	location varchar(128),
    region   varchar(4) null,
	`lat`    varchar(20),
	`long`   varchar(20),
    viewport varchar(128) default null,
	
	PRIMARY KEY (`id`),
    UNIQUE KEY `location` (`location`,`region`)	
) ENGINE = MEMORY DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
    `id_sha1` varchar(64) primary key not null,
 	`ts` bigint(20) unsigned NOT NULL default '0',
	`data` mediumblob,
	KEY `ts` (`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `state` (
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `log_entries` (
    `id` bigint(20) unsigned NOT NULL auto_increment,
    `subject_guid` binary(24) NULL,
    `event_name` varchar(50) NOT NULL,
    `user_guid` binary(24) NULL,
    `time_created` bigint(20) unsigned NOT NULL,
    `ip_address` varchar(48) null,
    `message` varchar(127) null,
    `source` tinyint(4) not null default 0,
    PRIMARY KEY  (`id`),
    KEY `event_name` (`event_name`),
    KEY `subject_guid` (`subject_guid`),
    KEY `user_guid` (`user_guid`),
    KEY `time_created` (`time_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `feed_items` (
	`id` INT NOT NULL AUTO_INCREMENT,		
	`feed_name` varchar(128) NOT NULL,
	`subtype_id` varchar(63) NOT NULL,
	`subject_guid` binary(24) NOT NULL,
	`user_guid` binary(24) NOT NULL,
	`time_posted` int NOT NULL,
	`args` TEXT default NULL,
	PRIMARY KEY ( `id` ),
	KEY `feed_key` (`feed_name`, `time_posted`),
	KEY `user_guid` (`user_guid`),
    KEY `subtype_id` (`subtype_id`),
	KEY `subject_guid` (`subject_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 	

CREATE TABLE `outgoing_mail` (
	`id` INT NOT NULL AUTO_INCREMENT,		
    `notifier_guid` binary(24) NULL,
    `subscription_guid` binary(24) NULL,
	`to_guid` binary(24) NULL,
    `from_guid` binary(24) NULL,
    `time_created` int NULL,
    `subject` text null,
    `to_address` text NULL,
    `time_queued` int NOT NULL,
	`time_sent` int NULL,
    `status` tinyint(4) default 0,
    `error_message` text null,
    `serialized_mail` mediumtext null,    
	PRIMARY KEY ( `id` ),
	KEY `notifier_guid` (`notifier_guid`),
    KEY `subscription_guid` (`subscription_guid`),
	KEY `to_guid` (`to_guid`),
    KEY `from_guid` (`from_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `outgoing_sms` (
	`id` INT NOT NULL AUTO_INCREMENT,		
    `notifier_guid` binary(24) NULL,
    `subscription_guid` binary(24) NULL,
    `message` text null,
    `from_number` varchar(32) not NULL,
    `to_name` text null,
    `to_number` varchar(32) not NULL,
    `time_created` int NULL,
	`time_sent` int NULL,
    `message_type` tinyint(4) not null default 0,
    `status` tinyint(4) not null default 0,
    `error_message` text null,
    `time_sendable` int NULL,
	PRIMARY KEY ( `id` ),
    KEY `subscription_guid` (`subscription_guid`),
    KEY `notifier_guid` (`notifier_guid`),
    KEY `waiting` (`status`,`time_sendable`),
	KEY `from_number` (`from_number`),
    KEY `to_number` (`to_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `revisions` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `owner_guid` binary(24) NULL,
    `entity_guid` binary(24) NOT NULL,
    `time_created` int not null,
    `time_updated` int not null,
    `content` mediumtext not null,
    `publish_status` tinyint(4) not null default 0,
    KEY (`entity_guid`),
    KEY (`owner_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `not_found_redirects` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `pattern` varchar(127) not null,
    `replacement` varchar(127) not null,
    `order` int(11) not null,
    `container_guid` binary(24) NULL,
    KEY (`container_guid`,`order`),
    KEY (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `external_feeds` (
    <?php require 'schema/entity_columns.php'; ?>,
    `url` text NOT NULL,
    `title` text NOT NULL,
    `subtype_id` varchar(63) not null,
    `feed_url` text NOT NULL,    
    `update_status` tinyint(4) not null default 0,
    `time_next_update` bigint(20) unsigned not null default 0,
    `time_queued` bigint(20) unsigned null,
    `time_changed` bigint(20) unsigned null,
    `time_update_started` bigint(20) unsigned null,
    `time_update_complete` bigint(20) unsigned null,
    `time_last_error` bigint(20) unsigned null,
    `last_error` text,
    `consecutive_errors` int(11) null,
    KEY `feed_url` (`feed_url`(50)),
    KEY (`time_next_update`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `external_sites` (
    <?php require 'schema/entity_columns.php'; ?>,
    `url` text NOT NULL,
    `title` text NOT NULL,
    `order` int(11) not null,
    `subtype_id` varchar(63) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `local_ids` (
    `guid` binary(24) not null PRIMARY KEY,
    `user_guid` binary(24) not null,
    `local_id` int not null,
    UNIQUE KEY `local_id_key` (`user_guid`,`local_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sms_subscriptions` (
    <?php require 'schema/entity_columns.php'; ?>,
    `subtype_id` varchar(63) not null,	
    `language` varchar(4) null,
    `phone_number` varchar(32) not null,
    `local_id` int not null,
    `name` text not null,
    `last_notification_time` bigint(20) unsigned NOT NULL default 0,
    `num_notifications` int(11) NOT NULL default 0,
    KEY `subtype_id` (`subtype_id`),
    UNIQUE KEY `local_id_key` (`phone_number`, `local_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `email_subscriptions` (
    <?php require 'schema/entity_columns.php'; ?>,
    `subtype_id` varchar(63) not null,	
    `language` varchar(4) null,
    `email` text not null,
    `name` text not null,
    `last_notification_time` bigint(20) unsigned NOT NULL default 0,
    `num_notifications` int(11) NOT NULL default 0,
    KEY `subtype_id` (`subtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `scheduled_events` (
    <?php require 'schema/entity_columns.php'; ?>,
    `subtype_id` varchar(63) not null,	
    `rrule` text not null,
    `start_time` int null,
    `end_time` int null,
    `prev_time` int null,
    `next_time` int null,    
    KEY `next_time` (`next_time`),
    KEY `subtype_id` (`subtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sms_app_state` (
    `id` int(11) NOT NULL auto_increment primary key,
    `phone_number` varchar(32) NOT NULL,
    `time_created` int not null,
    `time_updated` int not null,
    `active` tinyint(4) not null,
    UNIQUE KEY (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_scopes` (
    <?php require 'schema/entity_columns.php'; ?>,
    `description` varchar(63) not null,	
    `order` int not null default 0,
    `filters_json` text not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `permissions` (
    <?php require 'schema/entity_columns.php'; ?>,
    `subtype_id` varchar(63) not null,    
    `flags` tinyint(4) not null default 0,
    KEY `subtype_id` (`subtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
