CREATE TABLE `interface_languages` (
  <?php require 'schema/entity_columns.php'; ?>,
  `code` varchar(4) NOT NULL,
  `name` varchar(64) NOT NULL,
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `interface_groups` (
  <?php require 'schema/entity_columns.php'; ?>,
  `name` varchar(64) NOT NULL,
  `num_keys` int(11) not null default 0,
  UNIQUE KEY `group_name` (`container_guid`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `interface_group_metadata` (
    `id` int(11) auto_increment primary key,
    `name` varchar(64) NOT NULL,
    `description` text,
    `default_status` tinyint(4) default 1,
    KEY `name` (`name`)
);

CREATE TABLE `interface_keys` (
  <?php require 'schema/entity_columns.php'; ?>,
  `name` varchar(64) NOT NULL,
  `num_translations` int(11) not null default 0,
  `language_guid` bigint(20) unsigned NOT NULL,  
  `best_translation` text null,
  `best_translation_guid` bigint(20) unsigned NOT NULL,  
  UNIQUE KEY `key_name` (`container_guid`,`name`),
  KEY `language_name` (`language_guid`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `interface_translations` (
  <?php require 'schema/entity_columns.php'; ?>,  
  `language_guid` bigint(20) unsigned NOT NULL,  
  `value` text NOT NULL collate utf8_bin,
  `score` int(11) NOT NULL default 0,
  `default_value` text not null collate utf8_bin,
  KEY `language_guid` (`language_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `translation_votes` (
  <?php require 'schema/entity_columns.php'; ?>,  
  `score` int(11) NOT NULL default 0,
  `language_guid` bigint(20) unsigned NOT NULL,
  KEY `language_guid` (`language_guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `translator_stats` (
  <?php require 'schema/entity_columns.php'; ?>,  
  `score` int(11) NOT NULL default 0,
  `num_translations` int(11) NOT NULL default 0,
  `num_votes` int(11) NOT NULL default 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;