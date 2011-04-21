alter table widgets add `num_comments` int not null default 0;

insert ignore into widgets ( 
    `guid`,  
	`owner_guid`,
    `container_guid`,
	`time_created`,
	`time_updated`,
    `status`,	
  `content`,
  `thumbnail_url`,        
  `language`,
  `widget_name`,
  `subclass`,
  `menu_order`,
  `in_menu`,
  `handler_arg`,
  `title`,
  `num_comments`
) select 
    n.`guid`,  
	n.`owner_guid`,
    w.`guid`,
	n.`time_created`,
	n.`time_updated`,
    n.`status`,	
  n.`content`,
  n.`thumbnail_url`,        
  n.`language`,
  CONCAT('post_',n.`guid`),
  'Post',
  1000,
  0,
  '',
  '',
  n.`num_comments`
  FROM news_updates n
   INNER JOIN users u on n.container_guid = u.guid
   INNER JOIN widgets w on w.container_guid = u.guid   
   where w.subclass = 'News' and (w.status = 1 or w.guid = 8597);