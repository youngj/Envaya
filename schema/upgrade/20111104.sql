ALTER TABLE sms_subscriptions ADD `name` text not null;

CREATE TABLE `scheduled_events` (
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
    `rrule` text not null default '',
    `start_time` int null,
    `end_time` int null,
    `prev_time` int null,
    `next_time` int null,    
    KEY `next_time` (`next_time`),
    KEY `subtype_id` (`subtype_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;