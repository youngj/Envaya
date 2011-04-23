alter table entities drop key `subtype`;
alter table users drop key `subtype`;
alter table entities change `subtype_id` `subtype_id` varchar(63) not null;
alter table entities change `subtype_id` `subtype_id` varchar(63) not null;