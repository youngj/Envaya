ALTER TABLE translations add `html` tinyint not null default 0;

CREATE TABLE `files_entity` (
  `guid` bigint(20) unsigned  NOT NULL,
  
  `group_name` varchar(32) NOT NULL,
  `filename` varchar(64) NOT NULL,
  `width` int NULL,
  `height` int NULL,
  `size` varchar(32) NULL,
  `mime` varchar(32) NULL,

  PRIMARY KEY  (`guid`),
  KEY `group_name` (`group_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE users_entity ADD   `notify_days` int default 14;
ALTER TABLE users_entity ADD   `last_notify_time` int default null;
