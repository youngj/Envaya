ALTER TABLE widgets ADD `feed_guid` bigint(20) unsigned NULL;

UPDATE widgets w inner join metadata m on m.entity_guid = w.guid
    set w.feed_guid = m.value where m.name = 'feed_guid';
    
ALTER TABLE `files` ADD `metadata_json` text null;
ALTER TABLE `comments` ADD `metadata_json` text null;
ALTER TABLE `widgets` ADD `metadata_json` text null;
ALTER TABLE `users` ADD `metadata_json` text null;
ALTER TABLE `external_feeds` ADD `metadata_json` text null;
ALTER TABLE `external_sites` ADD `metadata_json` text null;
ALTER TABLE `sms_subscriptions` ADD `metadata_json` text null;
ALTER TABLE `email_subscriptions` ADD `metadata_json` text null;
ALTER TABLE `scheduled_events` ADD `metadata_json` text null;
ALTER TABLE `user_scopes` ADD `metadata_json` text null;
ALTER TABLE `permissions` ADD `metadata_json` text null;

ALTER TABLE `email_templates` ADD `metadata_json` text null;
ALTER TABLE `sms_templates` ADD `metadata_json` text null;

ALTER TABLE `discussion_messages` ADD `metadata_json` text null;
ALTER TABLE `discussion_topics` ADD `metadata_json` text null;

ALTER TABLE `featured_sites` ADD `metadata_json` text null;
ALTER TABLE `featured_photos` ADD `metadata_json` text null;

ALTER TABLE `org_relationships` ADD `metadata_json` text null;

ALTER TABLE `translation_languages` ADD `metadata_json` text null;
ALTER TABLE `interface_groups` ADD `metadata_json` text null;
ALTER TABLE `translation_keys` ADD `metadata_json` text null;
ALTER TABLE `translation_strings` ADD `metadata_json` text null;
ALTER TABLE `translation_votes` ADD `metadata_json` text null;
ALTER TABLE `translator_stats` ADD `metadata_json` text null;
ALTER TABLE `translation_key_comments` ADD `metadata_json` text null;

ALTER TABLE widgets ADD `uniqid` varchar(63) null;
ALTER TABLE discussion_topics ADD `uniqid` varchar(63) null;
ALTER TABLE discussion_messages ADD `uniqid` varchar(63) null;