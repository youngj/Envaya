
ALTER TABLE `outgoing_sms` ADD `message_type` tinyint(4) not null default 0;
ALTER TABLE `outgoing_sms` ADD `status` tinyint(4) not null default 0;
ALTER TABLE `outgoing_sms` ADD `time_sendable` int NULL;
AlTER TABLE `outgoing_sms` ADD KEY `waiting` (`status`,`time_sendable`);