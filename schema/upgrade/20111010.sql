CREATE TABLE `sms_app_state` (
    `id` int(11) NOT NULL auto_increment primary key,
    `phone_number` varchar(32) NOT NULL,
    `time_created` int not null,
    `time_updated` int not null,
    `active` tinyint(4) not null,
    UNIQUE KEY (`phone_number`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;
