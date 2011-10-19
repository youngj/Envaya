ALTER TABLE `sms_subscriptions` ADD `notification_type` tinyint(4) not null default 0;

UPDATE `sms_subscriptions` SET notification_type = 2 where description like 'G%';