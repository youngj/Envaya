DROP TABLE `geocode_cache`;
CREATE TABLE `geocode_cache` (
	id     int(11)     auto_increment,
	location varchar(128),
    region   varchar(4) null,
	`lat`    varchar(20),
	`long`   varchar(20),
	
	PRIMARY KEY (`id`),
    UNIQUE KEY `location` (`location`,`region`)	
) ENGINE = MEMORY DEFAULT CHARSET=utf8;
