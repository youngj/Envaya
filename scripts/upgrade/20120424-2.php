<?php
    require_once "start.php";      
    
    $old_db = 'envaya_old';
    $new_db = 'envaya';
    
    $classes = array_merge(array_values(PrefixRegistry::all_classes()), 
        array('ContentRevision',
        'FeedItem',
        'InvitedEmail',
        'LogEntry',
        'NotFoundRedirect',
        'OutgoingMail',
        'OutgoingSMS',
        'SharedEmail',        
        'SMS_AppState',
        'SMS_State',
        'UserDomainName',
        'UserPhoneNumber')
    );
      
    foreach ($classes as $class)
    {
        $table_name = $class::$table_name;
        
        $sql = "INSERT INTO $new_db.$table_name ";
        
        $attributes = $class::get_table_attributes();
        
        $attributes[$class::$primary_key] = null;
        
        $columns = array();
        $selects = array();
        $joins =  array();
        $join_num = 0;
        
        foreach ($attributes as $name => $default)
        {
            
        
            $columns[] = "`$name`";
            
            if ($name == 'guid' && is_subclass_of($class,'Entity'))
            {
                $selects[] = "t.new_guid";
                
                if (in_array($class, array('Widget','ReportResponse','ReportDefinition','DiscussionTopic')))
                {
                    $columns[] = "`tid`";
                    $selects[] = "t.guid";                
                }                                
            }
            else if ($class == 'Widget' && $name == 'user_guid')
            {
                $selects[] = "'TEMP'";
                // skip
            }
            else if ($class == 'Widget' && $name == 'local_id')
            {
                $selects[] = "t.guid";
            }
            else if ($class == 'UploadedFile' && $name == 'metadata_json')
            {
                $selects[] = "CONCAT('{\"folder_name\":\"', t.owner_guid, '\"}')"; // avoid breaking URLs
            }
            else if ($class == 'FeedItem' && $name == 'subject_guid')
            {
                $joins[] = "\n   inner join $old_db.entities e$join_num on e$join_num.guid = t.`$name`";
                $selects[] = "e$join_num.new_guid";                
                $join_num++;
            }
            else if (strpos($name, 'guid') !== false)
            {
                $joins[] = "\n   left outer join $old_db.entities e$join_num on e$join_num.guid = t.`$name`";
                $selects[] = "e$join_num.new_guid";                
                $join_num++;
            }
            else
            {
                $selects[] = "t.`$name`";
            }
            
        }
        
        $sql .= "(". implode(', ', $columns). ")";
        
        $sql .= "\n  SELECT ".implode(', ', $selects). "\n  FROM $old_db.$table_name t ".implode(" ", $joins).";";
        
        echo "$sql\n";
    }    
    
    echo "INSERT IGNORE INTO $new_db.state (name, value) SELECT name, value FROM $old_db.state;\n";
    
    echo "UPDATE $new_db.widgets wn SET user_guid = container_guid WHERE container_guid is not null;\n";    
    echo "UPDATE $new_db.widgets w1 INNER JOIN $new_db.widgets w2 ON w1.user_guid = w2.guid SET w1.user_guid = w2.container_guid WHERE w1.user_guid like 'WI%';\n";
    echo "UPDATE $new_db.widgets w1 INNER JOIN $new_db.widgets w2 ON w1.user_guid = w2.guid SET w1.user_guid = w2.container_guid WHERE w1.user_guid like 'WI%';\n";
    echo "UPDATE $new_db.widgets w1 INNER JOIN $new_db.widgets w2 ON w1.user_guid = w2.guid SET w1.user_guid = w2.container_guid WHERE w1.user_guid like 'WI%';\n";

    echo "UPDATE $new_db.widgets wn INNER JOIN $old_db.entities e ON e.guid = wn.handler_arg 
        SET wn.handler_arg = e.new_guid 
        WHERE (wn.subtype_id = 'reports.widget.reportresponse' or wn.subtype_id = 'reports.widget.reportdefinition') and length(wn.handler_arg) < 10;\n";
    
    echo "UPDATE $new_db.feed_items f inner join $old_db.entities e on e.guid = substr(feed_name,6)
        set feed_name = concat('user=', e.new_guid) 
        where feed_name like 'user=%' and length(feed_name) < 20;\n";
    
    echo "UPDATE $new_db.translation_keys t inner join $old_db.entities e on e.guid = round(substr(t.name,8)) set name = concat(e.new_guid, substr(name, 8+length(round(substr(name,8))))) where name like 'entity:%';\n";
    
    echo "INSERT INTO $new_db.local_ids (`guid`,`user_guid`,`local_id`) SELECT `guid`,`user_guid`,`local_id` FROM $new_db.widgets where local_id is not null;";
