CREATE TABLE `outgoing_mail` (
	`id` INT NOT NULL AUTO_INCREMENT,		
	`email_guid` bigint(20) NULL,
	`user_guid` bigint(20) NULL,
    `subject` text null,
    `to_address` text NULL,
    `time_queued` int NOT NULL,
	`time_sent` int NULL,
    `status` tinyint(4) default 0,
    `error_message` text null,
    `serialized_mail` mediumtext null,    
	PRIMARY KEY ( `id` ),
	KEY `email_guid` (`email_guid`),
	KEY `user_guid` (`user_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8; 	
