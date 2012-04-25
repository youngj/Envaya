<?php
    require_once "start.php";      
    
    $filters_map = array(
        'Approval' => 'Query_Filter_User_Approval',
        'Region' => 'Query_Filter_User_Region',
        'Inactive' => 'Query_Filter_User_Inactive',
        'UserType' => 'Query_Filter_User_Type',
        'Country' => 'Query_Filter_User_Country',
    );
    
    $items = array_merge(
        UserScope::query()->filter(),
        EmailTemplate::query()->filter(),
        SMSTemplate::query()->filter()
    );
    
    foreach ($items as $item)
    {
        $filters = json_decode($item->filters_json, true);        
        
        if (is_array($filters))
        {
            foreach ($filters as &$filter)
            {
                if (isset($filter['subclass']))
                {
                    $cls = $filters_map[$filter['subclass']];
                
                    $filter['subtype_id'] = $cls::get_subtype_id();
                    
                    unset($filter['subclass']);
                }
            }
        }
        
        $item->filters_json = json_encode($filters);
        $item->save();    
    }
    
    