RENAME TABLE log_entries TO log_entries_old;

CREATE TABLE `log_entries` (
    `id` bigint(20) unsigned NOT NULL auto_increment,
    `subject_guid` bigint(20) unsigned NULL,
    `event_name` varchar(50) NOT NULL,
    `user_guid` bigint(20) unsigned NULL,
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

ALTER TABLE permissions ADD `flags` tinyint(4) not null default 0;