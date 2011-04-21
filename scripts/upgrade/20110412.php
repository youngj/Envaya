<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    $orgs = Organization::query()->where("custom_icon = 1 OR (custom_header <> '' and custom_header <> '0')")->filter();
    
    function get_header_props($org)
    {
        $file = new UploadedFile();
        $file->owner_guid = $org->guid;
        $file->filename = "headerlarge.jpg";                    
        
        $url = $file->get_url();
        
        $header_props = json_decode($org->custom_header, true);
        
        return array(
            'size' => 'large',
            'url' => url_with_param($url, 't', $org->time_updated),
            'width' => $header_props['width'],
            'height' => $header_props['height']
        );
    }
    
    function get_icon_props($org, $size)
    {
        $file = new UploadedFile();
        $file->owner_guid = $org->guid;
        $file->filename = "icon$size.jpg";                    
        $url = $file->get_url();
        $sizeArray = getimagesize($url);
        if ($sizeArray)
        {        
            return array(
                'size' => $size,
                'url' => url_with_param($url, 't', $org->time_updated),
                'width' => $sizeArray[0],
                'height' => $sizeArray[1]
            );
        }
        return null;
    }
    
    foreach ($orgs as $org)
    {
        echo "{$org->get_url()}\n";
        if ($org->custom_header && !$org->migrated_custom_header)
        {
            $org->header_json = json_encode(get_header_props($org));
            $org->migrated_custom_header = true;
            $org->save();
            
        }
        
        if ($org->custom_icon && !$org->migrated_custom_icon)
        {
            $props_list = array();
            foreach (array('small','medium','large') as $size)
            {
                $props = get_icon_props($org, $size);
                if ($props)
                {
                    $props_list[] = $props;
                }
            }
            $org->icons_json = json_encode($props_list);
            $org->migrated_custom_icon = true;
            $org->save();
        }
    }