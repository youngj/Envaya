CREATE TABLE `translation_languages` (
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
  `code` varchar(4) NOT NULL,
  `name` varchar(64) NOT NULL,
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO translation_languages (
    guid,owner_guid,container_guid,time_created,time_updated,status,
    code,name
)
SELECT guid,owner_guid,container_guid,time_created,time_updated,status,
    code,name
 FROM interface_languages;

CREATE TABLE `translation_keys` (
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
  `subtype_id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `num_translations` int(11) not null default 0,
  `language_guid` bigint(20) unsigned NOT NULL,  
  `best_translation` text null,
  `best_translation_guid` bigint(20) unsigned NOT NULL,  
  KEY `key_name` (`container_guid`,`name`),
  UNIQUE KEY `language_name` (`language_guid`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT ignore INTO translation_keys (
    guid,owner_guid,container_guid,time_created,time_updated,status,
    subtype_id,name,num_translations,language_guid,best_translation,best_translation_guid
)
SELECT guid,owner_guid,container_guid,time_created,time_updated,status,
    'translate.interface.key',name,num_translations,language_guid,best_translation,best_translation_guid
    FROM interface_keys;

CREATE TABLE `translation_strings` (
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
  `language_guid` bigint(20) unsigned NOT NULL,  
  `value` text NOT NULL collate utf8_bin,
  `score` int(11) NOT NULL default 0,
  `default_value_hash` varchar(64) not null,
  `approval` tinyint(4) not null default 0,
  `approval_time` int(11) null,
  KEY `language_guid` (`language_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO translation_strings (
    guid,owner_guid,container_guid,time_created,time_updated,status,
    language_guid,value,score,default_value_hash,approval,approval_time
)
SELECT guid,owner_guid,container_guid,time_created,time_updated,status,
    language_guid,value,score,SHA1(default_value),0,0
    FROM interface_translations;

CREATE TABLE `translation_key_comments` (
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
  `language_guid` bigint(20) unsigned NULL default 0,  
  `key_name` varchar(64) NOT NULL,
  KEY `language_guid` (`language_guid`),
  KEY `key_name` (`key_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO translation_key_comments (
    guid,owner_guid,container_guid,time_created,time_updated,status,
    content,thumbnail_url,language,language_guid,key_name
)
SELECT guid,owner_guid,container_guid,time_created,time_updated,status,
    content,thumbnail_url,language,language_guid,key_name
    FROM interface_key_comments;
