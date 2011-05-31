alter table email_templates add `num_sent` int(11) not null default 0;
alter table email_templates add `time_last_sent` int(11) null;

update entities set subtype_id = 'contact.email.template' where subtype_id = 'core.email.template';
update interface_groups set name = 'contact' where name = 'email';

update interface_keys set name = 'contact:unsubscribe' where name = 'email:change';
update interface_keys set name = 'contact:here' where name = 'email:here';