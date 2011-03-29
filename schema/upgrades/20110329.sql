alter table entities add `status` tinyint(4) not null default 1;
update entities set status = 0 where enabled = 'no';

CREATE TABLE `revisions` (
    `id` INT NOT NULL AUTO_INCREMENT primary key,		
    `owner_guid` bigint(20) unsigned NOT NULL,
    `entity_guid` bigint(20) unsigned NOT NULL,
    `time_created` int not null,
    `time_updated` int not null,
    `content` mediumtext not null,
    `status` tinyint(4) not null,
    KEY (`entity_guid`),
    KEY (`owner_guid`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

alter table reports change `status` `report_status` tinyint(4)  NOT NULL default 0;