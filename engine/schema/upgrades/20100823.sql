ALTER TABLE translations drop primary key;
ALTER TABLE translations ADD `id` int auto_increment not null primary key;
ALTER TABLE translations ADD `owner_guid` bigint(20) unsigned NOT NULL default 0;
ALTER TABLE translations ADD `container_guid` bigint(20) unsigned NOT NULL default 0;
ALTER TABLE translations ADD `time_updated` int(11) NOT NULL default 0;
ALTER TABLE translations change `guid` `guid` bigint(20) unsigned  NOT NULL default 0;

UPDATE translations t INNER JOIN entities e on e.guid = t.guid 
    SET t.owner_guid = e.owner_guid, t.container_guid = e.container_guid, t.time_updated = e.time_updated
    WHERE e.`subtype` = 5;

ALTER TABLE translations ADD unique key (`container_guid`,`property`,`lang`,`owner_guid`,`html`);