ALTER TABLE sms_state ADD `user_guid` bigint(20) unsigned not null default 0;
ALTER TABLE sms_state ADD KEY (`user_guid`);

update user_phone_numbers set phone_number = concat('255',substr(phone_number,2)) where length(phone_number) = 10 and phone_number like '0%';