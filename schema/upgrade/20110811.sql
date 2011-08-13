
CREATE TABLE `user_phone_numbers` (
    `id` int(11) NOT NULL auto_increment,
    `phone_number` varchar(32) not null,
    `last_digits` int not null default 0,
	`user_guid` bigint(20) unsigned  NOT NULL,
	`confirmed` tinyint(4) NOT NULL default 0,
    PRIMARY KEY  (`id`),
    KEY (`phone_number`),
    KEY (`last_digits`),
	KEY (`user_guid`)
);

INSERT INTO user_phone_numbers (id,phone_number,last_digits,user_guid,confirmed)
    SELECT id,phone_number,last_digits,org_guid,confirmed FROM org_phone_numbers;
    
drop TABLE `sms_state`;

CREATE TABLE `sms_state` (
    `id` int(11) NOT NULL auto_increment,
    `service_id` varchar(32) not null,
    `phone_number` varchar(32) not null,
	`time_updated` int NOT NULL default 0,
	`value` text not null,
    PRIMARY KEY  (`id`),
    UNIQUE KEY (`phone_number`,`service_id`)
);