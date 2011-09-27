CREATE TABLE `local_ids` (
    `guid` bigint(20) unsigned not null PRIMARY KEY,
    `user_guid` bigint(20) unsigned not null,
    `local_id` int not null,    
    UNIQUE KEY `local_id_key` (`user_guid`,`local_id`)
);