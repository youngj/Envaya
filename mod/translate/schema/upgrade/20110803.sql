alter table translation_keys add key `latest_in_lang` (`language_guid`, `time_updated`, `guid`);

alter table translation_strings add `source` tinyint(4) not null default 0;

update translation_strings set `source` = 1 where owner_guid <> 0;
update translation_strings t inner join translation_keys k on k.guid = t.container_guid
    set t.source = 2 where t.owner_guid = 0 and k.subtype_id = 'translate.interface.key';
    
update translation_strings t inner join translation_keys k on k.guid = t.container_guid
    set t.source = 3 where t.owner_guid = 0 and k.subtype_id = 'translate.entity.key';    