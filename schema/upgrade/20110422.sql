alter table entities change `subtype` `subtype` int(11) null;
alter table entities add `subtype_id` varchar(63) null;
alter table entities add KEY `subtype_id` (`subtype_id`);

update entities set `subtype_id` = 'core.unknown' where `subtype_id` is null;
update entities set `subtype_id` = 'core.user' where `subtype` = 0;
update entities set `subtype_id` = 'core.file' where `subtype` = 1;
update entities set `subtype_id` = 'core.widget' where `subtype` = 3;
update entities set `subtype_id` = 'core.user.org' where `subtype` = 4;
update entities set `subtype_id` = 'core.widget' where `subtype` = 7;
update entities set `subtype_id` = 'core.featured.site' where `subtype` = 12;
update entities set `subtype_id` = 'core.email.template' where `subtype` = 13;
update entities set `subtype_id` = 'core.comment' where `subtype` = 16;
update entities set `subtype_id` = 'core.featured.photo' where `subtype` = 17;
update entities set `subtype_id` = 'core.user.org.relation' where `subtype` = 19;
update entities set `subtype_id` = 'core.discussion.message' where `subtype` = 21;
update entities set `subtype_id` = 'core.discussion.topic' where `subtype` = 22;
update entities set `subtype_id` = 'reports.definition' where `subtype` = 14;
update entities set `subtype_id` = 'reports.report' where `subtype` = 15;

alter table users change `subtype` `subtype` int(11) null;
alter table users add `subtype_id` varchar(63) null;
alter table users add KEY `subtype_id` (`subtype_id`);

update users set `subtype_id` = 'core.unknown' where `subtype_id` is null;
update users set `subtype_id` = 'core.user' where `subtype` = 0;
update users set `subtype_id` = 'core.user.org' where `subtype` = 4;
