<?php
    
    $entity = $vars['entity'];   
    
    $org = $entity->get_member_organization();
    
    if ($org)
    {
        echo view_entity($org);
    }
    else
    {    
        $icon = view('graphics/icon', array(
            'entity' => $entity,
            'size' => 'small',
        ));

        $name = $entity->name;
        
        $url = $entity->get_url();
        $link_open = $url ? "<a href='".escape($url)."'>" : '';
        $link_close = $url ? "</a>" : '';

        $info = "<div><b>$link_open".escape($name)."$link_close</b></div>";
        
        if (!$entity->org_guid && $entity->email)
        {
            $info .= "<div>".view('output/email', array('value' => $entity->email))."</div>";
        }
        
        $icon = "$link_open$icon$link_close";

        echo view('search/listing',array('icon' => $icon, 'info' => $info));
    }
