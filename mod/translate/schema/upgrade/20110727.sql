alter table translation_keys add `best_translation_approval` tinyint(4) not null default 0;

update translation_keys k inner join translation_strings t on t.guid = k.best_translation_guid
    set k.best_translation_approval = t.approval;