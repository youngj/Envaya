alter table widgets add `subclass` varchar(32) NULL;
update widgets set `subclass` = substr(`handler_class`,15) where `subclass` is null and `handler_class` is not null;
alter table entities change `subtype` `subtype` int(11) not null;