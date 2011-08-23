ALTER TABLE `sessions` DROP PRIMARY KEY;
ALTER TABLE `sessions` ADD KEY `session` (`session`);
ALTER TABLE `sessions` CHANGE `session` `session` VARCHAR(255) null;
ALTER TABLE `sessions` ADD `id_sha1` VARCHAR(64) null;
ALTER TABLE `sessions` ADD UNIQUE KEY `id_sha1` (`id_sha1`);
UPDATE `sessions` SET `id_sha1` = sha1(`session`) WHERE `id_sha1` IS NULL;

