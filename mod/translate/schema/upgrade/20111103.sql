ALTER TABLE translation_keys ADD `best_translation_hash` varchar(64) null;
UPDATE translation_keys k INNER JOIN translation_strings t ON t.guid = k.best_translation_guid
    SET k.best_translation_hash = t.default_value_hash;
