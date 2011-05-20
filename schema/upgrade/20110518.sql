alter table external_feeds add `title` text NULL;
alter table external_feeds add `feed_url` text NULL;

CREATE TABLE `external_sites` (
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
    `url` text NOT NULL,
    `title` text NOT NULL,
    `order` int(11) not null,
    `subtype_id` varchar(63) not null
) ENGINE = MYISAM DEFAULT CHARSET=utf8;