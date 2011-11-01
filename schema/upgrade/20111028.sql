CREATE TABLE `permissions` (
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
    `subtype_id` varchar(63) not null,	
    KEY `subtype_id` (`subtype_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

ALTER TABLE users add `password_time` int(11) NULL;

UPDATE users set subtype_id = 'core.user.person', setup_state = 5 where subtype_id = 'core.user';
UPDATE entities set subtype_id = 'core.user.person' where subtype_id = 'core.user';