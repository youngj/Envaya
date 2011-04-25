alter table widgets add `time_published` int(11) null;
alter table widgets add key `news_key` (`container_guid`,`time_published`,`guid`);
alter table widgets change `widget_name` `widget_name` varchar(127) NOT NULL;
alter table widgets change `title` `title` varchar(127) NULL;
alter table widgets add key `name_key` (`container_guid`,`widget_name`);
update widgets set time_published = time_created where time_published is null and publish_status=1;

CREATE TABLE `external_feeds` (
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
    `subtype_id` varchar(63) not null
) ENGINE = MYISAM DEFAULT CHARSET=utf8;