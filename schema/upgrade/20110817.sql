CREATE TABLE `outgoing_sms` (
	`id` INT NOT NULL AUTO_INCREMENT,		
    `message` text null,
    `from_number` varchar(32) not NULL,
    `to_name` text null,
    `to_number` varchar(32) not NULL,
    `time_created` int NULL,
	`time_sent` int NULL,
	PRIMARY KEY ( `id` ),
	KEY `from_number` (`from_number`),
    KEY `to_number` (`to_number`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;
