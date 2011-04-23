CREATE TABLE `not_found_redirects` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `pattern` varchar(127) not null,
    `replacement` varchar(127) not null,
    `order` int(11) not null
) ENGINE = MYISAM DEFAULT CHARSET=utf8;