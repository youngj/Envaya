alter table not_found_redirects add `container_guid` bigint(20) unsigned NOT NULL default 0;
alter table not_found_redirects add KEY (`container_guid`,`order`);
alter table not_found_redirects add KEY (`order`);