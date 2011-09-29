ALTER TABLE sms_subscriptions ADD `last_notification_time` int(11) NOT NULL default 0;
ALTER TABLE sms_subscriptions ADD `num_notifications` int(11) NOT NULL default 0;
	