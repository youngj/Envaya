CREATE TABLE `sms_subscriptions` (
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
    `description` text NOT NULL,
    `language` varchar(4) null,
    `phone_number` varchar(32) not null,
    `local_id` int not null,
    UNIQUE KEY `local_id_key` (`phone_number`, `local_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;
