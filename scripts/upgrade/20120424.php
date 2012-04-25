<?php
    require_once "start.php";      
    
    $old_db = 'envaya_old';
    
    echo "ALTER TABLE $old_db.entities ADD new_guid binary(24) null;\n";
    echo "ALTER TABLE $old_db.entities ADD UNIQUE KEY new_guid (`new_guid`);\n";        
    
    foreach (PrefixRegistry::all_classes() as $prefix => $class)
    {
        $table_name = $class::$table_name;
        
        echo "ALTER TABLE $old_db.$table_name ADD new_guid binary(24) null;\n";
        echo "UPDATE $old_db.$table_name SET new_guid = CONCAT('$prefix', lpad(hex(rand() * 1000000000000), 13,'0'), lpad(guid,9,'0'));\n";                
        echo "ALTER TABLE $old_db.$table_name ADD UNIQUE KEY new_guid (`new_guid`);\n";                
        echo "UPDATE $old_db.entities e INNER JOIN $old_db.$table_name t on e.guid = t.guid SET e.new_guid = t.new_guid;\n";        
    }    
    