CREATE TABLE `shared_emails` (
    `id` int(11) auto_increment primary key not null,
    `email` varchar(128) default null,
    `user_guid` bigint(20) unsigned NOT NULL,
    `time_shared` int(11) default null,
    `url` text default null,
    key (`email`),
    key (`user_guid`),
    key (`time_shared`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

alter table `users` add `design_json` text default null;

alter TABLE `outgoing_mail` add `to_guid` bigint(20) NULL;
alter TABLE `outgoing_mail` add `from_guid` bigint(20) NULL;
alter TABLE `outgoing_mail` add key `to_guid` (`to_guid`);
alter TABLE `outgoing_mail` add key `from_guid` (`from_guid`);
update outgoing_mail set to_guid = user_guid;