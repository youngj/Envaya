CREATE TABLE `log_entries` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`object_id` bigint(20) NULL,
	`subtype_id` varchar(63) NULL,
	`event_name` varchar(50) NOT NULL,
	`user_guid` bigint(20) NOT NULL,
	`time_created` int(11) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `event_name` (`event_name`),
    KEY `object_id` (`object_id`),
    KEY `subtype_id` (`subtype_id`),
	KEY `user_guid` (`user_guid`),
	KEY `time_created` (`time_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;