ALTER TABLE `log_entries` ADD `subject_guid` bigint(20) unsigned NULL;

ALTER TABLE log_entries ADD `ip_address` varchar(48) null;
ALTER TABLE log_entries ADD `message` varchar(127) null;
ALTER TABLE log_entries ADD `source` tinyint(4) not null default 0;

ALTER TABLE log_entries ADD KEY `subject_guid` (`subject_guid`);

ALTER TABLE log_entries ENGINE = InnoDB;

ALTER TABLE permissions ADD `flags` tinyint(4) not null default 0;