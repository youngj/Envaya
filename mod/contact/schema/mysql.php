CREATE TABLE `email_templates` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>, 
  `subject` text default null,
  `from` text default null,
  `num_sent` int(11) not null default 0,
  `time_last_sent` int(11) null,
  `filters_json` text default null
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
