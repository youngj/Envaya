CREATE TABLE `discussion_messages` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>,
    `status` tinyint(4) NOT NULL DEFAULT 1,
    `message_id` varchar(128) default '',
    `subject` text,
    `from_name` text,
    `from_location` text,
    `from_email` varchar(128) default '',
    `time_posted` bigint(20) unsigned,    
    KEY (`message_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `discussion_topics` (
    <?php require 'schema/entity_columns.php'; ?>,
    `first_message_guid` binary(24) NULL,
    `subject` text,
    `language` varchar(4) default null,
    `last_time_posted` bigint(20) unsigned default 0,
    `last_from_name` text,
    `num_messages` int(11) default 0,
    `snippet` text 
) ENGINE = MYISAM DEFAULT CHARSET=utf8;
