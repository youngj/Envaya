ALTER TABLE `feed_items` ADD `subtype_id` varchar(63) NULL;
ALTER TABLE `feed_items` CHANGE `action_name` `action_name` varchar(32) NULL;

UPDATE feed_items SET subtype_id = 'reports.feeditem.reportresponse' where action_name = 'report';
UPDATE feed_items SET subtype_id = 'core.feeditem.relation' where action_name = 'relationship';
UPDATE feed_items SET subtype_id = 'core.feeditem.discussion.message' where action_name = 'message';
UPDATE feed_items SET subtype_id = 'core.feeditem.home.edit' where action_name = 'edithome';
UPDATE feed_items SET subtype_id = 'core.feeditem.widget.edit' where action_name = 'editwidget';
UPDATE feed_items SET subtype_id = 'core.feeditem.widget.new' where action_name = 'newwidget';
UPDATE feed_items SET subtype_id = 'core.feeditem.news' where action_name = 'news';
UPDATE feed_items SET subtype_id = 'core.feeditem.news.multi' where action_name = 'newsmulti';
UPDATE feed_items SET subtype_id = 'core.feeditem.register' where action_name = 'register';
