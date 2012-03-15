CREATE TABLE `translation_languages` (
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

CREATE TABLE `translation_keys` (
  <?php require 'schema/entity_columns.php'; ?>,
  `subtype_id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `num_translations` int(11) not null default 0,
  `language_guid` bigint(20) unsigned NOT NULL,  
  `best_translation` text null,
  `best_translation_guid` bigint(20) unsigned NOT NULL,  
  `best_translation_hash` varchar(64) null,
  `best_translation_approval` tinyint(4) not null default 0,
  `best_translation_source` tinyint(4) not null default 0,
  KEY `key_name` (`container_guid`,`name`),
  UNIQUE KEY `language_name` (`language_guid`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `translation_strings` (
  <?php require 'schema/entity_columns.php'; ?>,  
  `language_guid` bigint(20) unsigned NOT NULL,  
  `value` text NOT NULL collate utf8_bin,
  `score` int(11) NOT NULL default 0,
  `default_value_hash` varchar(64) not null,
  `approval` tinyint(4) not null default 0,
  `approval_time` int(11) null,
  `source` tinyint(4) not null default 0,
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

CREATE TABLE `translation_key_comments` (
  <?php require 'schema/entity_columns.php'; ?>,  
  <?php require 'schema/content_columns.php'; ?>,  
  `language_guid` bigint(20) unsigned NULL default 0,  
  `key_name` varchar(64) NOT NULL,
  KEY `language_guid` (`language_guid`),
  KEY `key_name` (`key_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;