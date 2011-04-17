
alter table news_updates change `content` `content`  mediumtext NOT NULL;
alter table news_updates change `data_types` `data_types` tinyint(4) not null default 0;

alter table comments change `data_types` `data_types` tinyint(4) not null default 0;

alter table featured_sites change `data_types` `data_types` tinyint(4) not null default 0;

alter table email_templates change `content` `content` mediumtext NOT NULL;
alter table email_templates change `data_types` `data_types` tinyint(4) not null default 0;

alter table widgets change `content` `content` mediumtext NOT NULL;
alter table widgets change `data_types` `data_types` tinyint(4) not null default 0;

alter table org_relationships change `content` `content` mediumtext NOT NULL;
alter table org_relationships add `data_types` tinyint(4) not null default 0;
update org_relationships set data_types = 4 where data_types = 0 and content <> '';

alter table discussion_messages change `content` `content` mediumtext NOT NULL;
alter table discussion_messages add `language` varchar(4) default null;
