ALTER TABLE `widgets` ADD `subtype_id` varchar(63) NULL;

UPDATE widgets SET subtype_id = 'core.widget.contact' WHERE subclass = 'Contact';
UPDATE widgets SET subtype_id = 'core.widget.post' WHERE subclass = 'Post';
UPDATE widgets SET subtype_id = 'core.widget.post.facebook' WHERE subclass = 'FacebookPost';
UPDATE widgets SET subtype_id = 'core.widget.post.feed' WHERE subclass = 'FeedItem';
UPDATE widgets SET subtype_id = 'core.widget.post.rss' WHERE subclass = 'RSSItem';
UPDATE widgets SET subtype_id = 'core.widget.post.sms' WHERE subclass = 'SMSPost';
UPDATE widgets SET subtype_id = 'core.widget.post.tweet' WHERE subclass = 'Tweet';
UPDATE widgets SET subtype_id = 'core.widget.history' WHERE (subclass = 'Generic' and widget_name = 'history');
UPDATE widgets SET subtype_id = 'core.widget.projects' WHERE (subclass = 'Generic' and widget_name = 'projects');
UPDATE widgets SET subtype_id = 'core.widget.generic' WHERE subclass = 'Generic' and subtype_id is null;
UPDATE widgets SET subtype_id = 'core.widget.hardcoded' WHERE subclass = 'Hardcoded';
UPDATE widgets SET subtype_id = 'core.widget.home' WHERE subclass = 'Home';
UPDATE widgets SET subtype_id = 'core.widget.links' WHERE subclass = 'Links';
UPDATE widgets SET subtype_id = 'core.widget.location' WHERE subclass = 'Location';
UPDATE widgets SET subtype_id = 'core.widget.menu' WHERE subclass = 'Menu';
UPDATE widgets SET subtype_id = 'core.widget.mission' WHERE subclass = 'Mission';
UPDATE widgets SET subtype_id = 'core.widget.news' WHERE subclass = 'News';
UPDATE widgets SET subtype_id = 'core.widget.personprofile' WHERE subclass = 'PersonProfile';
UPDATE widgets SET subtype_id = 'core.widget.sectors' WHERE subclass = 'Sectors';
UPDATE widgets SET subtype_id = 'core.widget.team' WHERE subclass = 'Team';
UPDATE widgets SET subtype_id = 'core.widget.updates' WHERE subclass = 'Updates';

UPDATE widgets SET subtype_id = 'core.widget.network' WHERE subclass = 'Network';
UPDATE widgets SET subtype_id = 'core.widget.discussions' WHERE subclass = 'Discussions';


UPDATE widgets SET subtype_id = 'reports.widget.reportresponses' WHERE subclass = 'Reports';
UPDATE widgets SET subtype_id = 'reports.widget.reportdefinitions' WHERE subclass = 'ReportDefinitions';

UPDATE entities e INNER JOIN widgets w on w.guid = e.guid SET e.subtype_id = w.subtype_id where w.subtype_id <> '';