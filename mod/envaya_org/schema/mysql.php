CREATE TABLE `featured_sites` (
    <?php require 'schema/entity_columns.php'; ?>,
    <?php require 'schema/content_columns.php'; ?>, 
  `image_url` text default null,
  `active` tinyint(4) default 0  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `featured_photos` (
    <?php require 'schema/entity_columns.php'; ?>,
  `user_guid`  binary(24) NULL,  
  `image_url` text not null,
  `x_offset` int not null default 0,
  `y_offset` int not null default 0,
  `weight` float not null default 1,
  `href` varchar(127) default null,
  `caption` text default null,
  `org_name` varchar(127) default null,  
  `language` varchar(4) default null,
  `active` tinyint(4) default 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
