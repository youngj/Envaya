CREATE TABLE `email_templates` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>, 
  `subject` text default null,
  `from` text default null,
  `active` tinyint(4) NOT NULL default 0 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
