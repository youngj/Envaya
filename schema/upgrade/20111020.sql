ALTER TABLE outgoing_mail ADD `notifier_guid` bigint(20) NULL;
ALTER TABLE outgoing_sms ADD `notifier_guid` bigint(20) NULL;

ALTER TABLE outgoing_sms ADD KEY `notifier_guid` (`notifier_guid`);
ALTER TABLE outgoing_mail ADD KEY `notifier_guid` (`notifier_guid`);

ALTER TABLE outgoing_mail ADD `subscription_guid` bigint(20) NULL;
ALTER TABLE outgoing_sms ADD `subscription_guid` bigint(20) NULL;

ALTER TABLE outgoing_sms ADD KEY `subscription_guid` (`subscription_guid`);
ALTER TABLE outgoing_mail ADD KEY `subscription_guid` (`subscription_guid`);

UPDATE outgoing_mail SET notifier_guid = email_guid;

ALTER TABLE `sms_subscriptions` CHANGE `notification_type` `notification_type` tinyint(4) null;
ALTER TABLE `sms_subscriptions` CHANGE `description` `description` varchar(24) null;
ALTER TABLE `sms_subscriptions` ADD `subtype_id` varchar(63) not null default '';
ALTER TABLE sms_subscriptions ADD KEY `subtype_id` (`subtype_id`);

UPDATE sms_subscriptions set subtype_id = 'contact.subscription.sms.contact' where notification_type = 1;
UPDATE sms_subscriptions set subtype_id = 'core.subscription.sms.comments' where notification_type = 2;
UPDATE sms_subscriptions set subtype_id = 'core.subscription.sms.news' where notification_type = 16;

UPDATE sms_subscriptions set subtype_id = 'core.subscription.sms.comments' where description like 'N % %' and notification_type = 0;
UPDATE sms_subscriptions set subtype_id = 'core.subscription.sms.news' where description like 'N %'  and notification_type = 0;

UPDATE sms_subscriptions set owner_guid = container_guid where owner_guid = 0 AND 
    (subtype_id = 'core.subscription.sms.comments' or subtype_id = 'contact.subscription.sms.contact');

CREATE TABLE `email_subscriptions` (
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
    `language` varchar(4) null,
    `email` text not null,
    `name` text not null,
    `last_notification_time` int(11) NOT NULL default 0,
    `num_notifications` int(11) NOT NULL default 0,
    KEY `subtype_id` (`subtype_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sms_templates` (
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
  `num_sent` int(11) not null default 0,
  `time_last_sent` int(11) null,
  `filters_json` text default null
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
