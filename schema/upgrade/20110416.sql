insert into entities (
        `guid`,`subtype`
    ) select 
        `guid`, `subtype` 
    from envaya.entities;
   
insert into `files` (
        `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
        `group_name`,`storage`,`filename`,`width`,`height`,`size`,`mime`
    ) select 
        e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
        `group_name`,`storage`,`filename`,`width`,`height`,`size`,`mime`
    from envaya.entities e inner join envaya.files_entity f on f.guid = e.guid;    
    
insert into translations (
  `id`,
  `guid`,
  `owner_guid`,  
  `container_guid`,
  `time_updated`,
  `hash`,
  `property`,
  `lang`,
  `value`,
  `html`
  ) select 
  `id`,
  `guid`,
  `owner_guid`,  
  `container_guid`,
  `time_updated`,
  `hash`,
  `property`,
  `lang`,
  `value`,
  `html`    
  from envaya.translations;
 
-- skip interface_translations;

insert into news_updates (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
    `num_comments`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
    `num_comments`
from envaya.entities e inner join envaya.news_updates f on f.guid = e.guid;    

insert into `comments` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
  `name`,
  `email`,
  `location`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
  `name`,
  `email`,
  `location`
from envaya.entities e inner join envaya.`comments` f on f.guid = e.guid;    

insert into `featured_sites` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
  `user_guid`,  
  `image_url`,
  `active`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
  `user_guid`,  
  `image_url`,
  `active`
from envaya.entities e inner join envaya.`featured_sites` f on f.guid = e.guid;  

insert into `featured_photos` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
  `user_guid`,  
  `image_url`,
  `x_offset`,
  `y_offset`,
  `weight`,
  `href`,
  `caption`,
  `org_name`,  
  `language`,
  `active`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
  `user_guid`,  
  `image_url`,
  `x_offset`,
  `y_offset`,
  `weight`,
  `href`,
  `caption`,
  `org_name`,  
  `language`,
  `active`
from envaya.entities e inner join envaya.`featured_photos` f on f.guid = e.guid;  

insert into `email_templates` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
  `subject`,
  `from`,
  `active` 
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
  `subject`,
  `from`,
  `active` 
from envaya.entities e inner join envaya.`email_templates` f on f.guid = e.guid;  

insert into `widgets` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
  `widget_name`,
  `handler_class`,
  `menu_order`,
  `in_menu`,
  `handler_arg`,
  `title`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
  `widget_name`,
  `handler_class`,
  `menu_order`,
  `in_menu`,
  `handler_arg`,
  `title`
from envaya.entities e inner join envaya.`widgets` f on f.guid = e.guid;  

insert into invited_emails (
    `id`,
    `email`,
    `registered_guid`,
    `invite_code`,    
	`last_invited`,
	`num_invites`
) select 
    `id`,
    `email`,
    `registered_guid`,
    `invite_code`,    
	`last_invited`,
	`num_invites`
from envaya.invited_emails;

insert into `org_relationships` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
    `type`,    
    `subject_notified`,
    `invite_subject`,
    `subject_guid`,
    `subject_name`,    
    `subject_email`,
    `subject_phone`,
    `subject_website`,
    `subject_logo`,          
    `approval`,        
    `order`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
    `type`,    
    `subject_notified`,
    `invite_subject`,
    `subject_guid`,
    `subject_name`,    
    `subject_email`,
    `subject_phone`,
    `subject_website`,
    `subject_logo`,          
    `approval`,        
    `order`
from envaya.entities e inner join envaya.`org_relationships` f on f.guid = e.guid;  

insert into `users` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
  `subtype`,	
  `name`,
  `username`,
  `password`,
  `salt`,
  `email`,
  `phone_number`,
  `language`,
  `last_action`,
  `email_code`,
  `approval`,
  `setup_state`,
  `country`,
  `city`,  
  `icons_json`,
  `header_json`,
  `admin`,
  `latitude`,
  `longitude`,
  `timezone_id`,
  `region`,
  `theme`,  
  `last_notify_time`,
  `notifications`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
  e.`subtype`,	
  `name`,
  `username`,
  `password`,
  `salt`,
  `email`,
  `phone_number`,
  `language`,
  `last_action`,
  `email_code`,
  `approval`,
  `setup_state`,
  `country`,
  `city`,  
  `icons_json`,
  `header_json`,
  `admin`,
  `latitude`,
  `longitude`,
  `timezone_id`,
  `region`,
  `theme`,  
  `last_notify_time`,
  `notifications`
  from envaya.entities e inner join envaya.`users_entity` f on f.guid = e.guid;  

insert into org_domain_names (
    `id`,
	`guid`,
	`domain_name`
) select 
    `id`,
	`guid`,
	`domain_name`
from envaya.org_domain_names;  

insert into org_phone_numbers (
    `id`,
    `phone_number`,
    `last_digits`,
	`org_guid`,
	`confirmed`
) select 
    `id`,
    `phone_number`,
    `last_digits`,
	`org_guid`,
	`confirmed`
from envaya.org_phone_numbers;  

insert into sms_state (
    `id`,
    `phone_number`,
	`time_updated`,
	`args_json`
) select 
    `id`,
    `phone_number`,
	`time_updated`,
	`args_json`
from envaya.sms_state;  

insert into org_sectors (
  `id`,  
  `container_guid`,
  `sector_id` 
) select 
`id`,  
  `container_guid`,
  `sector_id` 
from envaya.org_sectors;  

insert into `metadata` (
    `id`,
    `entity_guid`,
    `name`,
    `value`,
    `value_type`
) select 
    `id`,
    `entity_guid`,
    `name`,
    `value`,
    `value_type`
from envaya.`metadata`;

-- skip cache

insert into `sessions` (
	`session`,
 	`ts`,
	`data`
) select 
	`session`,
 	`ts`,
	`data`
from envaya.`users_sessions`;  

delete from `state`;
insert into `state` (
  `name`,
  `value`
) select 
  `name`,
  `value`
from envaya.`datalists`;

insert into `system_log` (
  `id`,	
  `object_id`,
  `object_class`,   
  `event`,
  `user_guid`,
  `time_created`
) select 
  `id`,	
  `object_id`,
  `object_class`,   
  `event`,
  `performed_by_guid`,
  `time_created`
from envaya.`system_log`; 

insert into `feed_items` (
	`id`,		
	`feed_name`,
	`action_name`,
	`subject_guid`,
	`user_guid`,
	`time_posted`,
	`args`
) select 
	`id`,		
	`feed_name`,
	`action_name`,
	`subject_guid`,
	`user_guid`,
	`time_posted`,
	`args`
from envaya.`feed_items`; 
	   
insert into `outgoing_mail` (
	`id`,		
	`email_guid`,
	`user_guid`,
    `subject`,
    `to_address`,
    `time_queued`,
	`time_sent`,
    `status`,
    `error_message`,
    `serialized_mail`
) select 
	`id`,		
	`email_guid`,
	`user_guid`,
    `subject`,
    `to_address`,
    `time_queued`,
	`time_sent`,
    `status`,
    `error_message`,
    `serialized_mail`
from envaya.`outgoing_mail`; 
       
insert into `discussion_messages` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
     `content`,`data_types`, `language`,
    `message_id`,
    `subject`,
    `from_name`,
    `from_location`,
    `from_email`,
    `time_posted`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `content`,`data_types`, `language`,
    `message_id`,
    `subject`,
    `from_name`,
    `from_location`,
    `from_email`,
    `time_posted`
from envaya.entities e inner join envaya.`discussion_messages` f on f.guid = e.guid;  

insert into `discussion_topics` (
    `guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `first_message_guid`,
    `subject`,
    `last_time_posted`,
    `last_from_name`,
    `num_messages`,
    `snippet`
) select 
    e.`guid`,`owner_guid`,`container_guid`,`time_created`,`time_updated`,`status`,
    `first_message_guid`,
    `subject`,
    `last_time_posted`,
    `last_from_name`,
    `num_messages`,
    `snippet`
from envaya.entities e inner join envaya.`discussion_topics` f on f.guid = e.guid;  

insert into `revisions` (
    `id`,		
    `owner_guid`,
    `entity_guid`,
    `time_created`,
    `time_updated`,
    `content`,
    `status`
) select 
    `id`,		
    `owner_guid`,
    `entity_guid`,
    `time_created`,
    `time_updated`,
    `content`,
    `status`
from envaya.`revisions`;