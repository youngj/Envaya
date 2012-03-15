ALTER TABLE widgets change `content` `content` mediumtext NOT NULL;
ALTER TABLE comments change `content` `content` mediumtext NOT NULL;
ALTER TABLE discussion_messages change `content` `content` mediumtext NOT NULL;
ALTER TABLE featured_sites change `content` `content` mediumtext NOT NULL;
ALTER TABLE org_relationships change `content` `content` mediumtext NOT NULL;
ALTER TABLE report_comments change `content` `content` mediumtext NOT NULL;
ALTER TABLE translation_key_comments change `content` `content` mediumtext NOT NULL;
ALTER TABLE scheduled_events change `rrule` `rrule` text not null;
ALTER TABLE report_invitations change `email` `email` text not null;
ALTER TABLE report_invitations change `phone_number` `phone_number` text not null;
ALTER TABLE report_invitations change `rrule` `rrule` text not null;
ALTER TABLE report_versions change `xml` `xml` mediumtext NOT NULL;

ALTER TABLE `discussion_messages` CHANGE `subject` `subject` text;
ALTER TABLE `discussion_messages` CHANGE  `from_name`   `from_name` text;
ALTER TABLE `discussion_messages` CHANGE   `from_location`  `from_location` text;

ALTER TABLE `discussion_topics` CHANGE `subject` `subject` text;
ALTER TABLE `discussion_topics` CHANGE `last_from_name` `last_from_name` text;
ALTER TABLE `discussion_topics` CHANGE `snippet` `snippet` text;

ALTER TABLE translation_keys ADD `best_translation_source` tinyint(4) not null default 0;

UPDATE translation_keys k INNER JOIN translation_strings t on  k.best_translation_guid = t.guid
    set k.best_translation_source = t.source;
    