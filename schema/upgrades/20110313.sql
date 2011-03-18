ALTER TABLE entities DROP `type`;
ALTER TABLE entities DROP `site_guid`;

ALTER TABLE users_entity ADD `phone_number` varchar(128) NULL;

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

DROP TABLE `org_phone_numbers`;
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

CREATE TABLE `org_relationships` (
    `guid` bigint(20) unsigned NOT NULL,
    `type` tinyint(4) not null default 0,
    
    `subject_notified` tinyint(4) NOT NULL default 0,
    `invite_subject` tinyint(4) NOT NULL default 0,
    `subject_guid` bigint(20) unsigned NOT NULL,
    `subject_name` text default null,    
    `subject_email` varchar(128) default null,
    `subject_phone` varchar(128) default null,
    `subject_website` text default null,
    `subject_logo` text default null,    
    
    `content` text default null,
    `language` varchar(4) default null,
    `approval` int default 0,    
    
    `order` int default 0,
    PRIMARY KEY (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
