alter table widgets add `publish_status` tinyint(4) NOT NULL default 1;
update widgets set publish_status = 0 where `status` = 2;
update widgets set publish_status = 1 where `status` = 1;

alter table revisions add `publish_status` tinyint(4) not null default 0;
alter table revisions change `status` `status` tinyint(4) null;
update revisions set publish_status = status;